<?php

namespace App\Controller\Api;

use App\Entity\Collaborateur;
use App\Entity\Competence;
use App\Entity\CollaborateurCompetence;
use App\Repository\CollaborateurCompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/collaborateur-competences')]
class CollaborateurCompetenceController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(CollaborateurCompetenceRepository $repo): JsonResponse
    {
        return $this->json([
            'message' => 'Liste des liens collaborateur-compétence.',
            'data'    => $repo->findAll()
        ], 200, [], ['groups' => 'collabcomp:read']);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // --- Validation basique ---
            if (!isset($data['collaborateur'], $data['competence'], $data['niveau'])) {
                return $this->json(['message' => 'collaborateur, competence et niveau sont requis'], 422);
            }

            $collab = $em->getRepository(Collaborateur::class)->find($data['collaborateur']);
            $comp   = $em->getRepository(Competence::class)->find($data['competence']);

            if (!$collab || !$comp) {
                return $this->json(['message' => 'Collaborateur ou compétence introuvable'], 404);
            }

            if ($data['niveau'] < 0 || $data['niveau'] > 10) {
                return $this->json(['message' => 'niveau doit être compris entre 0 et 10'], 422);
            }

            // --- Création du lien ---
            $link = new CollaborateurCompetence();
            $link->setCollaborateur($collab)
                 ->setCompetence($comp)
                 ->setNiveau($data['niveau']);

            $em->persist($link);
            $em->flush();

            return $this->json([
                'message' => 'Lien collaborateur-compétence créé.',
                'data'    => $link
            ], 201, [], ['groups' => 'collabcomp:read']);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $link = $em->getRepository(CollaborateurCompetence::class)->find($id);
            if (!$link) {
                return $this->json(['message' => 'Lien non trouvé'], 404);
            }

            $data = json_decode($request->getContent(), true);
            if (isset($data['niveau'])) {
                if ($data['niveau'] < 0 || $data['niveau'] > 10) {
                    return $this->json(['message' => 'niveau doit être compris entre 0 et 10'], 422);
                }
                $link->setNiveau($data['niveau']);
            }

            $em->flush();

            return $this->json([
                'message' => 'Lien mis à jour.',
                'data'    => $link
            ], 200, [], ['groups' => 'collabcomp:read']);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $link = $em->getRepository(CollaborateurCompetence::class)->find($id);
        if (!$link) {
            return $this->json(['message' => 'Lien non trouvé'], 404);
        }

        $em->remove($link);
        $em->flush();

        return $this->json(['message' => 'Lien supprimé.'], 204);
    }
}
