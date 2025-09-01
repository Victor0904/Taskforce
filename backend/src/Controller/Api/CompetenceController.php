<?php

namespace App\Controller\Api;

use App\Entity\Competence;
use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/competences')]
class CompetenceController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(CompetenceRepository $repo): JsonResponse
    {
        try {
            $competences = $repo->findAll();
            return $this->json([
                'message' => 'Liste des compétences.',
                'data' => $competences
            ], 200, [], ['groups' => 'competence:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors du chargement des compétences: ' . $e->getMessage()
            ], 500);
        }
    }

        #[Route('/{id}', methods: ['GET'])]
    public function show(Competence $competence): JsonResponse
    {
        return $this->json([
            'message' => 'Compétence trouvée.',
            'data'    => $competence
        ], 200, [], ['groups' => 'competence:read']);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $competence = $serializer->deserialize($request->getContent(), Competence::class, 'json', ['groups' => 'competence:write']);
        $em->persist($competence);
        $em->flush();

        return $this->json(['message' => 'Compétence créée.', 'data' => $competence], 201, [], ['groups' => 'competence:read']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Competence $competence, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $serializer->deserialize($request->getContent(), Competence::class, 'json', [
            'object_to_populate' => $competence,
            'groups' => 'competence:write'
        ]);
        $em->flush();

        return $this->json(['message' => 'Compétence mise à jour.', 'data' => $competence], 200, [], ['groups' => 'competence:read']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Competence $competence, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($competence);
        $em->flush();

        return $this->json(['message' => 'Compétence supprimée.'], 204);
    }
}