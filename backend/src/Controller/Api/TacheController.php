<?php

namespace App\Controller\Api;

use App\Entity\Tache;
use App\Entity\Mission;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use App\Repository\TacheRepository;
use App\Service\TaskAssignmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/taches')]
class TacheController extends AbstractController
{
    public function __construct(
        private TaskAssignmentService $taskAssignmentService
    ) {}

    // ───────────────── GET tâches par projet (⚠️ bien placée DANS la classe)
    #[Route('/projet/{id}', name: 'taches_par_projet', methods: ['GET'])]
    public function getByProjet(int $id, TacheRepository $tacheRepo): JsonResponse
    {
        try {
            $taches = $tacheRepo->findBy(['mission' => $id]);
            return $this->json([
                'message' => 'Tâches du projet récupérées.',
                'data' => $taches
            ], 200, [], ['groups' => 'tache:read']);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors du chargement des tâches: ' . $e->getMessage()
            ], 500);
        }
    }

    // ───────────────── GET tâches par collaborateur (par email)
    #[Route('/collaborateur/email/{email}', name: 'taches_par_collaborateur_email', methods: ['GET'])]
    public function getByCollaborateurEmail(string $email, EntityManagerInterface $em): JsonResponse
    {
        // Trouver le collaborateur par email
        $collaborateur = $em->getRepository(Collaborateur::class)->findOneBy(['email' => $email]);
        
        if (!$collaborateur) {
            return new JsonResponse(['message' => 'Collaborateur non trouvé'], 404);
        }

        // Récupérer toutes les tâches assignées à ce collaborateur
        $taches = $em->getRepository(Tache::class)->findBy(['collaborateur' => $collaborateur]);

        return new JsonResponse([
            'message' => 'Tâches du collaborateur récupérées.',
            'data' => $taches
        ], 200);
    }

    // ───────────────── GET liste
    #[Route('', methods: ['GET'])]
    public function index(TacheRepository $repo): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Liste des tâches.',
            'data'    => $repo->findAll()
        ], 200);
    }

    // ───────────────── GET détail
    #[Route('/{id}', methods: ['GET'])]
    public function show(Tache $tache): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Tâche trouvée.',
            'data'    => $tache
        ], 200);
    }

    // ───────────────── POST création avec assignation automatique
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $required = [
                'titre', 'description', 'chargeEstimee', 'dateDebut',
                'dateFinPrevue', 'statut', 'mission', 'competenceRequise', 'priorite'
            ];

            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse(['message' => "Champ obligatoire manquant : $field"], 422);
                }
            }

            $mission = $em->getRepository(Mission::class)->find($data['mission']);
            $compet  = $em->getRepository(Competence::class)->find($data['competenceRequise']);

            if (!$mission || !$compet) {
                return new JsonResponse(['message' => 'Mission ou compétence introuvable.'], 404);
            }

            $tache = (new Tache())
                ->setTitre($data['titre'])
                ->setDescription($data['description'])
                ->setChargeEstimee($data['chargeEstimee'])
                ->setChargeReelle($data['chargeReelle'] ?? 0)
                ->setDateDebut(new \DateTime($data['dateDebut']))
                ->setDateFinPrevue(new \DateTime($data['dateFinPrevue']))
                ->setStatut($data['statut'])
                ->setPriorite($data['priorite'])
                ->setMission($mission)
                ->setCompetenceRequise($compet);

            // Persister la tâche d'abord pour avoir un ID
            $em->persist($tache);
            $em->flush();

            // Assigner automatiquement la tâche au meilleur collaborateur
            try {
                $collaborateurAssigne = $this->taskAssignmentService->assignTaskAutomatically($tache);
                
                return new JsonResponse([
                    'message' => 'Tâche créée et assignée automatiquement à ' . $collaborateurAssigne->getPrenom() . ' ' . $collaborateurAssigne->getNom(),
                    'data' => $tache,
                    'collaborateurAssigne' => [
                        'id' => $collaborateurAssigne->getId(),
                        'nom' => $collaborateurAssigne->getNom(),
                        'prenom' => $collaborateurAssigne->getPrenom(),
                        'email' => $collaborateurAssigne->getEmail()
                    ]
                ], 201);

            } catch (\Exception $e) {
                // Si l'assignation automatique échoue, on supprime la tâche
                $em->remove($tache);
                $em->flush();
                
                // Récupérer un message d'erreur détaillé
                $messageErreur = $this->taskAssignmentService->getAssignmentErrorMessage($tache);
                
                return new JsonResponse([
                    'message' => 'Impossible d\'assigner automatiquement la tâche',
                    'details' => $messageErreur,
                    'suggestion' => 'Vérifiez qu\'il y a des collaborateurs disponibles avec la compétence requise et une charge de travail acceptable'
                ], 422);
            }

        } catch (\Throwable $e) {
            return new JsonResponse(['message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    // ───────────────── PUT mise à jour
    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Tache $tache, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            foreach ([
                'titre', 'description', 'chargeEstimee', 'chargeReelle',
                'dateDebut', 'dateFinPrevue', 'statut', 'priorite'
            ] as $field) {
                if (isset($data[$field])) {
                    $setter = 'set' . ucfirst($field);
                    $value = in_array($field, ['dateDebut', 'dateFinPrevue'])
                        ? new \DateTime($data[$field])
                        : $data[$field];
                    $tache->$setter($value);
                }
            }

            if (isset($data['mission'])) {
                $mission = $em->getRepository(Mission::class)->find($data['mission']);
                if (!$mission) return new JsonResponse(['message' => 'Mission introuvable'], 404);
                $tache->setMission($mission);
            }

            if (isset($data['competenceRequise'])) {
                $compet = $em->getRepository(Competence::class)->find($data['competenceRequise']);
                if (!$compet) return new JsonResponse(['message' => 'Compétence introuvable'], 404);
                $tache->setCompetenceRequise($compet);
                
                // Si la compétence change, réassigner automatiquement la tâche
                if ($tache->getCompetenceRequise() !== $compet) {
                    try {
                        $this->taskAssignmentService->assignTaskAutomatically($tache);
                    } catch (\Exception $e) {
                        return new JsonResponse([
                            'message' => 'Impossible de réassigner la tâche avec la nouvelle compétence : ' . $e->getMessage()
                        ], 422);
                    }
                }
            }

            // Le collaborateur ne peut plus être modifié manuellement
            // Il est géré automatiquement par le système

            $em->flush();

            // Mettre à jour automatiquement la disponibilité du collaborateur assigné
            if ($tache->getCollaborateur()) {
                $this->taskAssignmentService->updateCollaborateurAvailability($tache->getCollaborateur());
            }

            return new JsonResponse([
                'message' => 'Tâche mise à jour.',
                'data' => $tache
            ], 200);

        } catch (\Throwable $e) {
            return new JsonResponse(['message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()], 500);
        }
    }

    // ───────────────── DELETE
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Tache $tache, EntityManagerInterface $em): JsonResponse
    {
        // Sauvegarder le collaborateur avant de supprimer la tâche
        $collaborateur = $tache->getCollaborateur();
        
        $em->remove($tache);
        $em->flush();

        // Mettre à jour automatiquement la disponibilité du collaborateur
        if ($collaborateur) {
            $this->taskAssignmentService->updateCollaborateurAvailability($collaborateur);
        }

        return new JsonResponse(['message' => 'Tâche supprimée avec succès.'], 204);
    }

    // ───────────────── POST réassignation manuelle (pour les cas d'urgence)
    #[Route('/{id}/reassigner', methods: ['POST'])]
    public function reassignTask(Tache $tache, EntityManagerInterface $em): JsonResponse
    {
        try {
            // Vérifier que la tâche a une compétence requise
            if (!$tache->getCompetenceRequise()) {
                return new JsonResponse(['message' => 'Impossible de réassigner une tâche sans compétence requise'], 422);
            }

            // Réassigner automatiquement la tâche
            $nouveauCollaborateur = $this->taskAssignmentService->assignTaskAutomatically($tache);

            // Mettre à jour automatiquement la disponibilité des collaborateurs concernés
            if ($tache->getCollaborateur()) {
                $this->taskAssignmentService->updateCollaborateurAvailability($tache->getCollaborateur());
            }
            $this->taskAssignmentService->updateCollaborateurAvailability($nouveauCollaborateur);

            return new JsonResponse([
                'message' => 'Tâche réassignée à ' . $nouveauCollaborateur->getPrenom() . ' ' . $nouveauCollaborateur->getNom(),
                'data' => $tache,
                'nouveauCollaborateur' => [
                    'id' => $nouveauCollaborateur->getId(),
                    'nom' => $nouveauCollaborateur->getNom(),
                    'prenom' => $nouveauCollaborateur->getPrenom(),
                    'email' => $nouveauCollaborateur->getEmail()
                ]
            ], 200);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Impossible de réassigner la tâche : ' . $e->getMessage()
            ], 422);
        }
    }
}
