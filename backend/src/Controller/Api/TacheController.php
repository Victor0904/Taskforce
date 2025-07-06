<?php

namespace App\Controller\Api;

use App\Entity\Tache;
use App\Entity\Mission;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/taches')]
class TacheController extends AbstractController
{
    // ───────────────── GET liste
    #[Route('', methods: ['GET'])]
    public function index(TacheRepository $repo): JsonResponse
    {
        return $this->json([
            'message' => 'Liste des tâches.',
            'data'    => $repo->findAll()
        ], 200, [], ['groups' => 'tache:read']);
    }

    // ───────────────── GET détail
    #[Route('/{id}', methods: ['GET'])]
    public function show(Tache $tache): JsonResponse
    {
        return $this->json([
            'message' => 'Tâche trouvée.',
            'data'    => $tache
        ], 200, [], ['groups' => 'tache:read']);
    }

    // ───────────────── POST création
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Champs obligatoires
            $required = [
                'titre', 'description',
                'chargeEstimee', 'dateDebut',
                'dateFinPrevue', 'statut',
                'mission', 'competenceRequise', 'priorite'
            ];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return $this->json(
                        ['message' => "Champ obligatoire manquant : $field"],
                        422
                    );
                }
            }

            // Récupération des entités liées
            $mission = $em->getRepository(Mission::class)->find($data['mission']);
            $compet  = $em->getRepository(Competence::class)->find($data['competenceRequise']);
            $collab  = isset($data['collaborateur'])
                ? $em->getRepository(Collaborateur::class)->find($data['collaborateur'])
                : null;

            if (!$mission || !$compet) {
                return $this->json(
                    ['message' => 'Mission ou compétence introuvable.'],
                    404
                );
            }
            if (isset($data['collaborateur']) && !$collab) {
                return $this->json(['message' => 'Collaborateur introuvable.'], 404);
            }

            // Création de la tâche
            $tache = (new Tache())
                ->setTitre($data['titre'])
                ->setDescription($data['description'])
                ->setChargeEstimee($data['chargeEstimee'])
                ->setChargeReelle($data['chargeReelle'] ?? 0)
                ->setDateDebut(new \DateTime($data['dateDebut']))
                ->setDateFinPrevue(new \DateTime($data['dateFinPrevue']))
                ->setStatut($data['statut'])
                ->setPriorite($data['priorite'])
                ->setMission($mission)
                ->setCompetenceRequise($compet)
                ->setCollaborateur($collab);

            $em->persist($tache);
            $em->flush();

            return $this->json(
                ['message' => 'Tâche créée avec succès.', 'data' => $tache],
                201,
                [],
                ['groups' => 'tache:read']
            );
        } catch (\Throwable $e) {
            return $this->json(
                ['message' => 'Erreur lors de la création de la tâche : ' . $e->getMessage()],
                500
            );
        }
    }

    // ───────────────── PUT mise à jour
    #[Route('/{id}', methods: ['PUT'])]
    public function update(
        Request $request,
        Tache $tache,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            // Mise à jour des champs simples s’ils sont présents
            foreach (
                ['titre', 'description', 'chargeEstimee', 'chargeReelle',
                 'dateDebut', 'dateFinPrevue', 'statut', 'priorite'] as $field
            ) {
                if (isset($data[$field])) {
                    $setter = 'set' . ucfirst($field);
                    $value  = in_array($field, ['dateDebut', 'dateFinPrevue'])
                        ? new \DateTime($data[$field])
                        : $data[$field];
                    $tache->$setter($value);
                }
            }

            // Relations (mission, competence, collaborateur)
            if (isset($data['mission'])) {
                $mission = $em->getRepository(Mission::class)->find($data['mission']);
                if (!$mission) {
                    return $this->json(['message' => 'Mission introuvable'], 404);
                }
                $tache->setMission($mission);
            }

            if (isset($data['competenceRequise'])) {
                $compet = $em->getRepository(Competence::class)->find($data['competenceRequise']);
                if (!$compet) {
                    return $this->json(['message' => 'Compétence introuvable'], 404);
                }
                $tache->setCompetenceRequise($compet);
            }

            if (array_key_exists('collaborateur', $data)) { // peut être null
                $collab = $data['collaborateur'] !== null
                    ? $em->getRepository(Collaborateur::class)->find($data['collaborateur'])
                    : null;
                if ($data['collaborateur'] !== null && !$collab) {
                    return $this->json(['message' => 'Collaborateur introuvable'], 404);
                }
                $tache->setCollaborateur($collab);
            }

            $em->flush();

            return $this->json(
                ['message' => 'Tâche mise à jour.', 'data' => $tache],
                200,
                [],
                ['groups' => 'tache:read']
            );
        } catch (\Throwable $e) {
            return $this->json(
                ['message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()],
                500
            );
        }
    }

    // ───────────────── DELETE
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Tache $tache, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($tache);
        $em->flush();

        return $this->json(['message' => 'Tâche supprimée avec succès.'], 204);
    }
}
