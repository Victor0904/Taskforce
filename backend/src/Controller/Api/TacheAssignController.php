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
        foreach ($tasks as $tache) {
            $collab = $service->assign($tache);
            if ($collab) {
                $resultats[] = [
                    'tache'        => $tache->getId(),
                    'collaborateur'=> $collab->getId()
                ];
            }
        }
        $em->flush();

        return $this->json([
            'message' => 'Assignation effectuée.',
            'data'    => $resultats
        ], 200);
    }
}
