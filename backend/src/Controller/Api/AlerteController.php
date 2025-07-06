<?php

namespace App\Controller\Api;

use App\Entity\Alerte;
use App\Repository\AlerteRepository;
use App\Repository\CollaborateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/alertes')]
class AlerteController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(AlerteRepository $repo): JsonResponse
    {
        $alertes = $repo->findAll();

        return $this->json([
            'message' => 'Liste des alertes récupérée avec succès.',
            'data' => $alertes
        ], 200, [], ['groups' => 'alerte:read']);
    }

    #[Route('/non-resolues', methods: ['GET'])]
    public function nonResolues(AlerteRepository $repo): JsonResponse
    {
        $alertes = $repo->findBy(['resolue' => false]);

        return $this->json([
            'message' => 'Liste des alertes non résolues.',
            'data' => $alertes
        ], 200, [], ['groups' => 'alerte:read']);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, CollaborateurRepository $collabRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['collaborateur_id'])) {
            return $this->json(['message' => 'collaborateur_id est requis.'], 400);
        }

        $collaborateur = $collabRepo->find($data['collaborateur_id']);

        if (!$collaborateur) {
            return $this->json(['message' => 'Collaborateur non trouvé.'], 404);
        }

        $alerte = new Alerte();
        $alerte->setType($data['type'] ?? 'surcharge');
        $alerte->setMessage($data['message'] ?? 'Problème détecté');
        $alerte->setResolue(false);
        $alerte->setCollaborateur($collaborateur);

        $em->persist($alerte);
        $em->flush();

        return $this->json([
            'message' => 'Alerte créée avec succès.',
            'data' => $alerte
        ], 201, [], ['groups' => 'alerte:read']);
    }

    #[Route('/{id}/resoudre', methods: ['PUT'])]
    public function resoudre(Alerte $alerte, EntityManagerInterface $em): JsonResponse
    {
        $alerte->setResolue(true);
        $em->flush();

        return $this->json([
            'message' => 'Alerte marquée comme résolue.',
            'data' => $alerte
        ], 200, [], ['groups' => 'alerte:read']);
    }
}
