<?php

namespace App\Controller\Api;

use App\Entity\Collaborateur;
use App\Repository\CollaborateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/collaborateurs')]
class CollaborateurController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(CollaborateurRepository $repo): JsonResponse
    {
        return $this->json([
            'message' => 'Liste des collaborateurs.',
            'data' => $repo->findAll()
        ], 200, [], ['groups' => 'collab:read']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Collaborateur $collaborateur): JsonResponse
    {
        return $this->json([
            'message' => 'Collaborateur trouvé.',
            'data' => $collaborateur
        ], 200, [], ['groups' => 'collab:read']);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        try {
            $collaborateur = $serializer->deserialize($request->getContent(), Collaborateur::class, 'json', ['groups' => 'collab:write']);
            $em->persist($collaborateur);
            $em->flush();

            return $this->json([
                'message' => 'Collaborateur créé avec succès.',
                'data' => $collaborateur
            ], 201, [], ['groups' => 'collab:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Collaborateur $collaborateur, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        try {
            $serializer->deserialize($request->getContent(), Collaborateur::class, 'json', [
                'object_to_populate' => $collaborateur,
                'groups' => 'collab:write'
            ]);
            $em->flush();

            return $this->json([
                'message' => 'Collaborateur mis à jour.',
                'data' => $collaborateur
            ], 200, [], ['groups' => 'collab:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Collaborateur $collaborateur, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($collaborateur);
            $em->flush();

            return $this->json([
                'message' => 'Collaborateur supprimé.'
            ], 204);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }
}
