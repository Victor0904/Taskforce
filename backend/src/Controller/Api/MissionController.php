<?php

namespace App\Controller\Api;

use App\Entity\Mission;
use App\Entity\Collaborateur;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/missions')]
class MissionController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(MissionRepository $repo): JsonResponse
    {
        return $this->json([
            'message' => 'Liste des missions.',
            'data' => $repo->findAll()
        ], 200, [], ['groups' => 'mission:read']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Mission $mission): JsonResponse
    {
        return $this->json([
            'message' => 'Mission trouvée.',
            'data' => $mission
        ], 200, [], ['groups' => 'mission:read']);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $responsable = $em->getRepository(Collaborateur::class)->find($data['responsable']);
            if (!$responsable) {
                return $this->json(['message' => 'Responsable introuvable'], 404);
            }

            $mission = new Mission();
            $mission->setTitre($data['titre']);
            $mission->setDescription($data['description']);
            $mission->setPriorite($data['priorite']);
            $mission->setDateDebut(new \DateTime($data['dateDebut']));
            $mission->setDateFinPrevue(new \DateTime($data['dateFinPrevue']));
            $mission->setStatut($data['statut']);
            $mission->setResponsable($responsable);

            $em->persist($mission);
            $em->flush();

            return $this->json([
                'message' => 'Mission créée avec succès.',
                'data' => $mission
            ], 201, [], ['groups' => 'mission:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la création de la mission : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Mission $mission, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $responsable = $em->getRepository(Collaborateur::class)->find($data['responsable']);
            if (!$responsable) {
                return $this->json(['message' => 'Responsable introuvable'], 404);
            }

            $mission->setTitre($data['titre']);
            $mission->setDescription($data['description']);
            $mission->setPriorite($data['priorite']);
            $mission->setDateDebut(new \DateTime($data['dateDebut']));
            $mission->setDateFinPrevue(new \DateTime($data['dateFinPrevue']));
            $mission->setStatut($data['statut']);
            $mission->setResponsable($responsable);

            $em->flush();

            return $this->json([
                'message' => 'Mission mise à jour.',
                'data' => $mission
            ], 200, [], ['groups' => 'mission:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Mission $mission, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($mission);
            $em->flush();

            return $this->json([
                'message' => 'Mission supprimée avec succès.'
            ], 204);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }
}
