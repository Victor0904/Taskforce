<?php

namespace App\Service;

use App\Entity\Tache;
use App\Entity\Collaborateur;
use Doctrine\ORM\EntityManagerInterface;

class TaskAssignmentService
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Assigne la tâche et retourne le collaborateur retenu (ou null si aucun).
     */
    public function assign(Tache $tache): ?Collaborateur
    {
        if ($tache->getCollaborateur()) {
            return $tache->getCollaborateur(); // déjà assignée
        }

        // 1. Récupère les couples collab/compétence correspondants
        $qb = $this->em->createQueryBuilder()
            ->select('c', 'cc')
            ->from(Collaborateur::class, 'c')
            ->join('c.competences', 'cc')
            ->where('cc.competence = :comp')
            ->andWhere('c.disponible = true')
            ->setParameter('comp', $tache->getCompetenceRequise());

        /** @var Collaborateur[] $cands */
        $cands = $qb->getQuery()->getResult();

        if (!$cands) {
            return null; // personne de disponible
        }

        // 2. Score = (10 - chargeActuelle) * 0.6 + niveau/10 * 0.4 – priorite/5 * 0.2
        $best = null;
        $bestScore = -INF;

        foreach ($cands as $cand) {
            $niveau = 0;
            foreach ($cand->getCompetences() as $link) {
                if ($link->getCompetence() === $tache->getCompetenceRequise()) {
                    $niveau = $link->getNiveau();
                    break;
                }
            }
            $score = (10 - $cand->getChargeActuelle()) * 0.6
                   + ($niveau / 10) * 0.4
                   - ($tache->getPriorite() / 5) * 0.2;

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $cand;
            }
        }

        if (!$best) {
            return null;
        }

        // 3. Assigne & met à jour la charge
        $tache->setCollaborateur($best);
        $best->setChargeActuelle(
            $best->getChargeActuelle() + $tache->getChargeEstimee()
        );

        // ❗ pas flush ici → laissé au contrôleur
        return $best;
    }
}
