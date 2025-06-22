<?php

namespace App\Controller\Api;

use App\Entity\Affectation;
use App\Form\AffectationType;
use App\Repository\AffectationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/affectations', name: 'api_affectations_')]
class AffectationApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(AffectationRepository $repo): JsonResponse
    {
        $data = array_map(function (Affectation $aff) {
            return [
                'id' => $aff->getId(),
                'mission' => $aff->getMission()?->getTitre(),
                'collaborateur' => $aff->getCollaborateur()?->getNomComplet(),
                'role' => $aff->getRole(),
                'date_debut' => $aff->getDateDebut()?->format('Y-m-d'),
                'date_fin' => $aff->getDateFin()?->format('Y-m-d'),
            ];
        }, $repo->findAll());

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $affectation = new Affectation();
        $form = $this->createForm(AffectationType::class, $affectation);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $em->persist($affectation);
            $em->flush();

            return $this->json(['message' => 'Affectation créée', 'id' => $affectation->getId()], 201);
        }

        return $this->json(['errors' => (string) $form->getErrors(true, false)], 400);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Affectation $aff): JsonResponse
    {
        return $this->json([
            'id' => $aff->getId(),
            'mission' => $aff->getMission()?->getTitre(),
            'collaborateur' => $aff->getCollaborateur()?->getNomComplet(),
            'role' => $aff->getRole(),
            'date_debut' => $aff->getDateDebut()?->format('Y-m-d'),
            'date_fin' => $aff->getDateFin()?->format('Y-m-d'),
        ]);
    }

    
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Affectation $aff, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($aff);
        $em->flush();
    
        return $this->json(['message' => 'Affectation supprimée']);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
public function update(Request $request, Affectation $affectation, EntityManagerInterface $em): JsonResponse
{
    $form = $this->createForm(AffectationType::class, $affectation);
    $form->submit(json_decode($request->getContent(), true), false); // ⚠️ false = PATCH-like (pas besoin de tous les champs)

    if ($form->isValid()) {
        $em->flush();
        return $this->json(['message' => 'Affectation mise à jour']);
    }

    return $this->json(['errors' => (string) $form->getErrors(true, false)], 400);
}

    
}
