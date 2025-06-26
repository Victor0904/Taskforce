<?php

namespace App\Controller\Api;

use App\Entity\Collaborateur;
use App\Form\CollaborateurType;
use App\Repository\CollaborateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/collaborateurs', name: 'api_collaborateurs_')]
class CollaborateurApiController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CollaborateurRepository $repo): JsonResponse
    {
        $collaborateurs = $repo->findAll();

        $data = array_map(function ($collab) {
            return [
                'id' => $collab->getId(),
                'nom' => $collab->getNom(),
                'prenom' => $collab->getPrenom(),
                'email' => $collab->getEmail(),
                'poste' => $collab->getPoste(),
                'actif' => $collab->isActif(),
                'date_naissance' => $collab->getDateNaissance()?->format('Y-m-d'),
                'competences' => array_map(fn($c) => ['id' => $c->getId(), 'nom' => $c->getNom()], $collab->getCompetences()->toArray()),
            ];
        }, $collaborateurs);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $collab = new Collaborateur();
        $form = $this->createForm(CollaborateurType::class, $collab);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $em->persist($collab);
            $em->flush();

            return $this->json(['message' => 'Collaborateur créé', 'id' => $collab->getId()], 201);
        }

        return $this->json(['errors' => (string) $form->getErrors(true, false)], 400);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Collaborateur $collab = null): JsonResponse
    {
        if (!$collab) {
            return $this->json(['error' => 'Collaborateur non trouvé'], 404);
        }

        return $this->json([
            'id' => $collab->getId(),
            'nom' => $collab->getNom(),
            'prenom' => $collab->getPrenom(),
            'email' => $collab->getEmail(),
            'poste' => $collab->getPoste(),
            'actif' => $collab->isActif(),
            'date_naissance' => $collab->getDateNaissance()?->format('Y-m-d'),
            'competences' => array_map(fn($c) => ['id' => $c->getId(), 'nom' => $c->getNom()], $collab->getCompetences()->toArray()),
        ]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Collaborateur $collab = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$collab) {
            return $this->json(['error' => 'Collaborateur non trouvé'], 404);
        }

        $form = $this->createForm(CollaborateurType::class, $collab);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $em->flush();
            return $this->json(['message' => 'Collaborateur mis à jour']);
        }

        return $this->json(['errors' => (string) $form->getErrors(true, false)], 400);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Collaborateur $collab = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$collab) {
            return $this->json(['error' => 'Collaborateur non trouvé'], 404);
        }

        $em->remove($collab);
        $em->flush();

        return $this->json(['message' => 'Collaborateur supprimé']);
    }
}
