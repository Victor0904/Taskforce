<?php

namespace App\Service;

use App\Entity\Tache;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use App\Repository\CollaborateurRepository;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskAssignmentService
{
    private const MAX_WORKLOAD_PERCENT = 80; // Limite de 80% de charge
    private const MAX_WORKLOAD_HOURS = 60; // 60 heures par semaine

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CollaborateurRepository $collaborateurRepository,
        private TacheRepository $tacheRepository
    ) {}

    /**
     * Assigne automatiquement une tâche au meilleur collaborateur disponible
     */
    public function assignTaskAutomatically(Tache $tache): ?Collaborateur
    {
        // Récupérer la compétence requise pour la tâche
        $competenceRequise = $tache->getCompetenceRequise();
        if (!$competenceRequise) {
            throw new \Exception('Une compétence est requise pour assigner automatiquement une tâche');
        }

        // Trouver tous les collaborateurs ayant la compétence requise
        $collaborateursCompetents = $this->findCollaborateursWithCompetence($competenceRequise);
        
        if (empty($collaborateursCompetents)) {
            throw new \Exception('Aucun collaborateur n\'a la compétence requise: ' . $competenceRequise->getNom());
        }

        // Filtrer par disponibilité et charge de travail
        $collaborateursDisponibles = $this->filterAvailableCollaborateurs($collaborateursCompetents, $tache);

        if (empty($collaborateursDisponibles)) {
            throw new \Exception('Aucun collaborateur disponible avec la compétence requise et une charge de travail acceptable');
        }

        // Sélectionner le meilleur collaborateur selon l'algorithme de scoring
        $meilleurCollaborateur = $this->selectBestCollaborateur($collaborateursDisponibles, $tache);

        // Assigner la tâche
        $tache->setCollaborateur($meilleurCollaborateur);
        $this->entityManager->flush();

        // Mettre à jour automatiquement la disponibilité du collaborateur assigné
        $updateInfo = $this->updateCollaborateurAvailability($meilleurCollaborateur);
        
        return $meilleurCollaborateur;
    }

    /**
     * Trouve tous les collaborateurs ayant une compétence spécifique
     */
    private function findCollaborateursWithCompetence(Competence $competence): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c')
           ->from(Collaborateur::class, 'c')
           ->join('c.competences', 'cc')
           ->join('cc.competence', 'comp')
           ->where('comp.id = :competenceId')
           ->andWhere('c.disponible = true')
           ->setParameter('competenceId', $competence->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * Filtre les collaborateurs par disponibilité et charge de travail
     */
    private function filterAvailableCollaborateurs(array $collaborateurs, Tache $tache): array
    {
        $collaborateursDisponibles = [];
        $collaborateursSurcharges = [];

        foreach ($collaborateurs as $collaborateur) {
            if (!$collaborateur->isDisponible()) {
                continue;
            }

            // Calculer la charge actuelle du collaborateur
            $chargeActuelle = $this->calculateCurrentWorkload($collaborateur);
            
            // Calculer la charge totale si on ajoute cette tâche
            $chargeTotale = $chargeActuelle + $tache->getChargeEstimee();
            
            // Vérifier que la charge ne dépasse pas 80% de 60h = 48h
            $chargeMaximale = (self::MAX_WORKLOAD_HOURS * self::MAX_WORKLOAD_PERCENT) / 100; // 48h
            
            if ($chargeTotale <= $chargeMaximale) {
                $collaborateursDisponibles[] = $collaborateur;
            } else {
                // Si la charge dépasse 80%, on l'ajoute à la liste des surchargés
                $collaborateursSurcharges[] = [
                    'collaborateur' => $collaborateur,
                    'chargeActuelle' => $chargeActuelle,
                    'chargeTotale' => $chargeTotale,
                    'pourcentage' => ($chargeTotale / self::MAX_WORKLOAD_HOURS) * 100
                ];
            }
        }

        // Si aucun collaborateur n'est disponible, essayer de trouver un collaborateur avec une charge acceptable
        if (empty($collaborateursDisponibles) && !empty($collaborateursSurcharges)) {
            // Trier par charge croissante et prendre le moins surchargé
            usort($collaborateursSurcharges, function($a, $b) {
                return $a['chargeTotale'] <=> $b['chargeTotale'];
            });
            
            // Vérifier si le moins surchargé peut accepter la tâche sans dépasser 100%
            $moinsSurcharge = $collaborateursSurcharges[0];
            if ($moinsSurcharge['chargeTotale'] <= self::MAX_WORKLOAD_HOURS) {
                $collaborateursDisponibles[] = $moinsSurcharge['collaborateur'];
            }
        }

        return $collaborateursDisponibles;
    }

    /**
     * Calcule la charge de travail actuelle d'un collaborateur
     */
    private function calculateCurrentWorkload(Collaborateur $collaborateur): float
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('SUM(t.chargeEstimee)')
           ->from(Tache::class, 't')
           ->where('t.collaborateur = :collaborateurId')
           ->andWhere('t.statut IN (:statutsActifs)')
           ->setParameter('collaborateurId', $collaborateur->getId())
           ->setParameter('statutsActifs', ['en_cours', 'en_attente', 'planifiée']);

        $result = $qb->getQuery()->getSingleScalarResult();
        
        return $result ? (float) $result : 0.0;
    }

    /**
     * Sélectionne le meilleur collaborateur selon un algorithme de scoring
     */
    private function selectBestCollaborateur(array $collaborateurs, Tache $tache): Collaborateur
    {
        $meilleurScore = -1;
        $meilleurCollaborateur = null;

        foreach ($collaborateurs as $collaborateur) {
            $score = $this->calculateCollaborateurScore($collaborateur, $tache);
            
            if ($score > $meilleurScore) {
                $meilleurScore = $score;
                $meilleurCollaborateur = $collaborateur;
            }
        }

        return $meilleurCollaborateur;
    }

    /**
     * Calcule un score pour un collaborateur basé sur plusieurs critères
     */
    private function calculateCollaborateurScore(Collaborateur $collaborateur, Tache $tache): float
    {
        $score = 0.0;

        // 1. Score basé sur le niveau de compétence (40% du score total)
        $niveauCompetence = $this->getCompetenceLevel($collaborateur, $tache->getCompetenceRequise());
        $score += ($niveauCompetence / 10) * 40;

        // 2. Score basé sur la charge de travail (30% du score total)
        // Plus la charge est faible, plus le score est élevé
        $chargeActuelle = $this->calculateCurrentWorkload($collaborateur);
        $chargeMaximale = (self::MAX_WORKLOAD_HOURS * self::MAX_WORKLOAD_PERCENT) / 100;
        $pourcentageCharge = ($chargeActuelle / $chargeMaximale) * 100;
        $score += (100 - $pourcentageCharge) * 0.3; // 30 points max

        // 3. Score basé sur la priorité des tâches existantes (20% du score total)
        $scorePriorite = $this->calculatePriorityScore($collaborateur);
        $score += $scorePriorite * 20;

        // 4. Score basé sur l'expérience (10% du score total)
        $scoreExperience = $this->calculateExperienceScore($collaborateur);
        $score += $scoreExperience * 10;

        return $score;
    }

    /**
     * Récupère le niveau de compétence d'un collaborateur pour une compétence donnée
     */
    private function getCompetenceLevel(Collaborateur $collaborateur, Competence $competence): int
    {
        foreach ($collaborateur->getCompetences() as $collabComp) {
            if ($collabComp->getCompetence()->getId() === $competence->getId()) {
                return $collabComp->getNiveau();
            }
        }
        return 0;
    }

    /**
     * Calcule un score basé sur la priorité des tâches existantes
     */
    private function calculatePriorityScore(Collaborateur $collaborateur): float
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('AVG(t.priorite)')
           ->from(Tache::class, 't')
           ->where('t.collaborateur = :collaborateurId')
           ->andWhere('t.statut IN (:statutsActifs)')
           ->setParameter('collaborateurId', $collaborateur->getId())
           ->setParameter('statutsActifs', ['en_cours', 'en_attente', 'planifiée']);

        $prioriteMoyenne = $qb->getQuery()->getSingleScalarResult();
        
        if (!$prioriteMoyenne) {
            return 1.0; // Pas de tâches = score parfait
        }

        // Normaliser la priorité (1 = haute priorité, 5 = basse priorité)
        // Plus la priorité est élevée, plus le score est élevé
        return (6 - $prioriteMoyenne) / 5; // Normalisé entre 0 et 1
    }

    /**
     * Calcule un score basé sur l'expérience du collaborateur
     */
    private function calculateExperienceScore(Collaborateur $collaborateur): float
    {
        // Compter le nombre de tâches terminées avec succès
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(t.id)')
           ->from(Tache::class, 't')
           ->where('t.collaborateur = :collaborateurId')
           ->andWhere('t.statut = :statutTermine')
           ->setParameter('collaborateurId', $collaborateur->getId())
           ->setParameter('statutTermine', 'terminee');

        $tachesTerminees = $qb->getQuery()->getSingleScalarResult();
        
        // Normaliser l'expérience (0-50 tâches = score 0-1)
        return min($tachesTerminees / 50, 1.0);
    }

    /**
     * Vérifie si une tâche peut être assignée sans dépasser la limite de charge
     */
    public function canAssignTask(Tache $tache, Collaborateur $collaborateur): bool
    {
        $chargeActuelle = $this->calculateCurrentWorkload($collaborateur);
        $chargeTotale = $chargeActuelle + $tache->getChargeEstimee();
        
        // Permettre jusqu'à 100% de charge (60h) en cas d'urgence
        return $chargeTotale <= self::MAX_WORKLOAD_HOURS;
    }

    /**
     * Récupère les statistiques de charge pour un collaborateur
     */
    public function getWorkloadStats(Collaborateur $collaborateur): array
    {
        $chargeActuelle = $this->calculateCurrentWorkload($collaborateur);
        $chargeMaximale = (self::MAX_WORKLOAD_HOURS * self::MAX_WORKLOAD_PERCENT) / 100;
        $pourcentageCharge = ($chargeActuelle / $chargeMaximale) * 100;
        $pourcentageChargeTotal = ($chargeActuelle / self::MAX_WORKLOAD_HOURS) * 100;
        
        return [
            'chargeActuelle' => $chargeActuelle,
            'chargeMaximale' => $chargeMaximale,
            'chargeMaximaleTotale' => self::MAX_WORKLOAD_HOURS,
            'pourcentageCharge' => $pourcentageCharge,
            'pourcentageChargeTotal' => $pourcentageChargeTotal,
            'disponible' => $pourcentageCharge < self::MAX_WORKLOAD_PERCENT,
            'surcharge' => $pourcentageChargeTotal > 60,
            'critique' => $pourcentageChargeTotal > 80
        ];
    }



    /**
     * Met à jour automatiquement le statut de disponibilité des collaborateurs
     * selon leur charge de travail
     */
    public function updateCollaborateursAvailability(): array
    {
        $updates = [];
        $collaborateurs = $this->collaborateurRepository->findAll();
        
        foreach ($collaborateurs as $collaborateur) {
            $stats = $this->getWorkloadStats($collaborateur);
            $ancienStatut = $collaborateur->isDisponible();
            $nouveauStatut = $stats['disponible'];
            
            // Mettre à jour le statut si nécessaire
            if ($ancienStatut !== $nouveauStatut) {
                $collaborateur->setDisponible($nouveauStatut);
                $updates[] = [
                    'collaborateur' => $collaborateur->getPrenom() . ' ' . $collaborateur->getNom(),
                    'ancienStatut' => $ancienStatut ? 'Disponible' : 'Indisponible',
                    'nouveauStatut' => $nouveauStatut ? 'Disponible' : 'Indisponible',
                    'chargeActuelle' => $stats['chargeActuelle'],
                    'pourcentageCharge' => round($stats['pourcentageCharge'], 1),
                    'raison' => $nouveauStatut ? 'Charge de travail acceptable' : 'Charge de travail > 80%'
                ];
            }
        }
        
        // Sauvegarder les changements
        if (!empty($updates)) {
            $this->entityManager->flush();
        }
        
        return $updates;
    }

    /**
     * Met à jour la disponibilité d'un collaborateur spécifique
     */
    public function updateCollaborateurAvailability(Collaborateur $collaborateur): array
    {
        $stats = $this->getWorkloadStats($collaborateur);
        $ancienStatut = $collaborateur->isDisponible();
        $nouveauStatut = $stats['disponible'];
        
        if ($ancienStatut !== $nouveauStatut) {
            $collaborateur->setDisponible($nouveauStatut);
            $this->entityManager->flush();
            
            return [
                'collaborateur' => $collaborateur->getPrenom() . ' ' . $collaborateur->getNom(),
                'ancienStatut' => $ancienStatut ? 'Disponible' : 'Indisponible',
                'nouveauStatut' => $nouveauStatut ? 'Disponible' : 'Indisponible',
                'chargeActuelle' => $stats['chargeActuelle'],
                'pourcentageCharge' => round($stats['pourcentageCharge'], 1),
                'raison' => $nouveauStatut ? 'Charge de travail acceptable' : 'Charge de travail > 80%'
            ];
        }
        
        return [];
    }

    /**
     * Récupère le message d'erreur détaillé pour l'assignation
     */
    public function getAssignmentErrorMessage(Tache $tache): string
    {
        $competenceRequise = $tache->getCompetenceRequise();
        if (!$competenceRequise) {
            return 'Une compétence est requise pour assigner automatiquement une tâche';
        }

        // Trouver tous les collaborateurs avec la compétence
        $collaborateursCompetents = $this->findCollaborateursWithCompetence($competenceRequise);
        
        if (empty($collaborateursCompetents)) {
            return "Aucun collaborateur n'a la compétence requise: " . $competenceRequise->getNom();
        }

        // Analyser la situation de chaque collaborateur
        $situation = [];
        foreach ($collaborateursCompetents as $collab) {
            $stats = $this->getWorkloadStats($collab);
            $chargeTotale = $stats['chargeActuelle'] + $tache->getChargeEstimee();
            $pourcentageTotal = ($chargeTotale / self::MAX_WORKLOAD_HOURS) * 100;
            
            if ($pourcentageTotal > 100) {
                $situation[] = sprintf(
                    "%s %s : %.1fh actuel + %.1fh tâche = %.1fh (%.1f%% de la semaine)",
                    $collab->getPrenom(),
                    $collab->getNom(),
                    $stats['chargeActuelle'],
                    $tache->getChargeEstimee(),
                    $chargeTotale,
                    $pourcentageTotal
                );
            }
        }

        if (!empty($situation)) {
            return sprintf(
                "Impossible d'assigner la tâche. Tous les collaborateurs compétents sont surchargés :\n%s\n\nVeuillez attendre qu'ils terminent leurs tâches en cours ou réduire la charge de cette tâche.",
                implode("\n", $situation)
            );
        }

        return "Aucun collaborateur disponible avec la compétence requise et une charge de travail acceptable";
    }
}
