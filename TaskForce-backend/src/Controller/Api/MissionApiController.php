<?php

namespace App\Controller\Api;

use App\Entity\Mission;
use App\Form\MissionType;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/missions', name: 'api_missions_')]
class MissionApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(MissionRepository $repository): JsonResponse
    {
        $missions = $repository->findAll();

        $data = array_map(function (Mission $mission) {
            return [
                'id' => $mission->getId(),
                'titre' => $mission->getTitre(),
                'description' => $mission->getDescription(),
                'statut' => $mission->getStatut(),
                'competences' => array_map(fn($c) => ['id' => $c->getId(), 'nom' => $c->getNom()], $mission->getCompetences()->toArray()),
                'date_debut' => $mission->getDateDebut()?->format('Y-m-d'),
                'date_fin' => $mission->getDateFin()?->format('Y-m-d'),
            ];
        }, $missions);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $mission = new Mission();
        $form = $this->createForm(MissionType::class, $mission);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $em->persist($mission);
            $em->flush();

            return $this->json([
                'message' => 'Mission créée',
                'id' => $mission->getId()
            ], 201);
        }

        return $this->json([
            'errors' => (string) $form->getErrors(true, false)
        ], 400);
    }
}
