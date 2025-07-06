<?php

namespace App\Controller\Api;

use App\Entity\HistoriqueTache;
use App\Repository\HistoriqueTacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/historiques')]
class HistoriqueTacheController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(HistoriqueTacheRepository $repo): JsonResponse
    {
        return $this->json([
            'message' => 'Liste des historiques de tâches.',
            'data' => $repo->findAll()
        ], 200, [], ['groups' => 'historique:read']);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        try {
            $historique = $serializer->deserialize($request->getContent(), HistoriqueTache::class, 'json', ['groups' => 'historique:write']);
            $em->persist($historique);
            $em->flush();

            return $this->json([
                'message' => 'Historique ajouté.',
                'data' => $historique
            ], 201, [], ['groups' => 'historique:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de l\'ajout : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, HistoriqueTache $historique, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        try {
            $serializer->deserialize($request->getContent(), HistoriqueTache::class, 'json', [
                'object_to_populate' => $historique,
                'groups' => 'historique:write'
            ]);
            $em->flush();

            return $this->json([
                'message' => 'Historique mis à jour.',
                'data' => $historique
            ], 200, [], ['groups' => 'historique:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(HistoriqueTache $historique, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($historique);
            $em->flush();

            return $this->json([
                'message' => 'Historique supprimé.'
            ], 204);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }
}
