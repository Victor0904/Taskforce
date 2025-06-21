<?php

namespace App\Controller\Api;

use App\Repository\CollaborateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CollaborateurApiController extends AbstractController
{
    #[Route('/api/collaborateurs', name: 'api_collaborateurs', methods: ['GET'])]
    public function index(CollaborateurRepository $repo): JsonResponse
    {
        $collaborateurs = $repo->findAll();

        $data = array_map(function ($collab) {
            return [
                'id' => $collab->getId(),
                'nom' => $collab->getNom(),
                'competences' => [], // on y reviendrasy
            ];
        }, $collaborateurs);

        return $this->json($data);
    }
}
