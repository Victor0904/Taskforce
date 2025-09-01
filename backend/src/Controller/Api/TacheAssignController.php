<?php

namespace App\Controller\Api;

use App\Entity\Tache;
use App\Repository\TacheRepository;
use App\Service\TaskAssignmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/taches')]
class TacheAssignController extends AbstractController
{
    #[Route('/assigner', methods: ['POST'])]
    public function assign(
        Request $request,
        TacheRepository $repo,
        TaskAssignmentService $service,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
        $payload = json_decode($request->getContent(), true) ?: [];
        $tasks   = [];

        if (isset($payload['tache'])) {
            $t = $repo->find($payload['tache']);
            if (!$t) {
                return $this->json(['message' => 'Tâche introuvable'], 404);
            }
            $tasks[] = $t;
        } else {
            $tasks = $repo->createQueryBuilder('t')
                ->where('t.collaborateur IS NULL')
                ->getQuery()
                ->getResult();
        }

        $resultats = [];
        $erreurs = [];
        
        foreach ($tasks as $tache) {
            try {
                // Vérifier que la tâche a une compétence requise
                if (!$tache->getCompetenceRequise()) {
                    $erreurs[] = "Tâche {$tache->getId()} ({$tache->getTitre()}): Pas de compétence requise définie";
                    continue;
                }
                
                $collab = $service->assignTaskAutomatically($tache);
                if ($collab) {
                    $resultats[] = [
                        'tache'        => $tache->getId(),
                        'collaborateur'=> $collab->getId(),
                        'nom'          => $collab->getPrenom() . ' ' . $collab->getNom()
                    ];
                }
            } catch (\Exception $e) {
                $erreurs[] = "Tâche {$tache->getId()} ({$tache->getTitre()}): " . $e->getMessage();
                error_log("Erreur assignation tâche {$tache->getId()}: " . $e->getMessage());
            }
        }
        $em->flush();

        $message = 'Assignation effectuée.';
        if (!empty($erreurs)) {
            $message .= ' Certaines tâches n\'ont pas pu être assignées.';
        }
        
        return $this->json([
            'message' => $message,
            'data'    => $resultats,
            'erreurs' => $erreurs,
            'totalTraitees' => count($tasks),
            'totalAssignees' => count($resultats),
            'totalErreurs' => count($erreurs)
        ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de l\'assignation en lot',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json([
            'message' => 'API TacheAssign fonctionne correctement',
            'status' => 'ok'
        ]);
    }
}
