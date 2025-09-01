<?php

namespace App\Controller\Api;

use App\Entity\Collaborateur;
use App\Repository\CollaborateurRepository;
use App\Service\UserAccountService;
use App\Service\TaskAssignmentService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/collaborateurs')]
class CollaborateurController extends AbstractController
{
    public function __construct(
        private UserAccountService $userAccountService,
        private TaskAssignmentService $taskAssignmentService
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(CollaborateurRepository $repo): JsonResponse
    {
        try {
            $collaborateurs = $repo->findAll();
            return $this->json([
                'message' => 'Liste des collaborateurs.',
                'data' => $collaborateurs
            ], 200, [], ['groups' => 'collab:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors du chargement des collaborateurs: ' . $e->getMessage()
            ], 500);
        }
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
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        try {
            $collaborateur = $serializer->deserialize($request->getContent(), Collaborateur::class, 'json', ['groups' => 'collab:write']);

            $errors = $validator->validate($collaborateur);
            if (count($errors) > 0) {
                $violations = [];
                foreach ($errors as $violation) {
                    $violations[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
                }
                return $this->json([
                    'message' => 'Validation failed',
                    'errors'  => $violations,
                ], 422);
            }

            // Validation des rôles autorisés
            $allowedRoles = ['Collaborateur', 'Manager', 'Chef de projet'];
            if (!in_array($collaborateur->getRole(), $allowedRoles)) {
                return $this->json([
                    'message' => 'Rôle non autorisé. Rôles autorisés : ' . implode(', ', $allowedRoles)
                ], 422);
            }

            $em->persist($collaborateur);
            $em->flush();

            // Créer automatiquement le compte utilisateur
            try {
                $user = $this->userAccountService->createOrUpdateUserAccount($collaborateur);
                
                return $this->json([
                    'message' => 'Collaborateur créé avec succès. Compte utilisateur créé avec email: ' . $user->getEmail() . ' et mot de passe: admin',
                    'data' => $collaborateur,
                    'userAccount' => [
                        'email' => $user->getEmail(),
                        'expiresAt' => $user->getExpiresAt()->format('Y-m-d H:i:s'),
                        'mustChangePassword' => $user->getMustChangePassword()
                    ]
                ], 201, [], ['groups' => 'collab:read']);
            } catch (\Exception $userError) {
                // Si la création du compte échoue, on supprime le collaborateur
                $em->remove($collaborateur);
                $em->flush();
                
                return $this->json([
                    'message' => 'Erreur lors de la création du compte utilisateur : ' . $userError->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Collaborateur $collaborateur, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        try {
            $serializer->deserialize($request->getContent(), Collaborateur::class, 'json', [
                'object_to_populate' => $collaborateur,
                'groups' => 'collab:write'
            ]);

            $errors = $validator->validate($collaborateur);
            if (count($errors) > 0) {
                $violations = [];
                foreach ($errors as $violation) {
                    $violations[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
                }
                return $this->json([
                    'message' => 'Validation failed',
                    'errors'  => $violations,
                ], 422);
            }

            // Validation des rôles autorisés
            $allowedRoles = ['Collaborateur', 'Manager', 'Chef de projet'];
            if (!in_array($collaborateur->getRole(), $allowedRoles)) {
                return $this->json([
                    'message' => 'Rôle non autorisé. Rôles autorisés : ' . implode(', ', $allowedRoles)
                ], 422);
            }

            $em->flush();

            // Mettre à jour le compte utilisateur
            try {
                $user = $this->userAccountService->createOrUpdateUserAccount($collaborateur);
                
                return $this->json([
                    'message' => 'Collaborateur mis à jour. Compte utilisateur mis à jour.',
                    'data' => $collaborateur,
                    'userAccount' => [
                        'email' => $user->getEmail(),
                        'expiresAt' => $user->getExpiresAt() ? $user->getExpiresAt()->format('Y-m-d H:i:s') : null,
                        'mustChangePassword' => $user->getMustChangePassword()
                    ]
                ], 200, [], ['groups' => 'collab:read']);
            } catch (\Exception $userError) {
                return $this->json([
                    'message' => 'Collaborateur mis à jour mais erreur lors de la mise à jour du compte utilisateur : ' . $userError->getMessage()
                ], 200);
            }
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Collaborateur $collaborateur, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($collaborateur);
        $em->flush();

        return $this->json(['message' => 'Collaborateur supprimé avec succès.'], 204);
    }

    /**
     * Récupère les compétences d'un collaborateur
     */
    #[Route('/{id}/competences', methods: ['GET'])]
    public function getCompetences(Collaborateur $collaborateur): JsonResponse
    {
        try {
            $competences = $collaborateur->getCompetences();
            
            return $this->json([
                'message' => 'Compétences du collaborateur récupérées',
                'data' => $competences
            ], 200, [], ['groups' => 'collab:read']);
            
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la récupération des compétences : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour automatiquement la disponibilité de tous les collaborateurs
     */
    #[Route('/update-availability', methods: ['POST'])]
    public function updateAllCollaborateursAvailability(): JsonResponse
    {
        try {
            $updates = $this->taskAssignmentService->updateCollaborateursAvailability();
            
            if (empty($updates)) {
                return $this->json([
                    'message' => 'Aucune mise à jour de disponibilité nécessaire',
                    'data' => []
                ], 200);
            }
            
            return $this->json([
                'message' => 'Disponibilité des collaborateurs mise à jour automatiquement',
                'data' => $updates,
                'totalUpdates' => count($updates)
            ], 200);
            
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la mise à jour de la disponibilité : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour automatiquement la disponibilité d'un collaborateur spécifique
     */
    #[Route('/{id}/update-availability', methods: ['POST'])]
    public function updateCollaborateurAvailability(Collaborateur $collaborateur): JsonResponse
    {
        try {
            $update = $this->taskAssignmentService->updateCollaborateurAvailability($collaborateur);
            
            if (empty($update)) {
                return $this->json([
                    'message' => 'Aucune mise à jour de disponibilité nécessaire pour ' . $collaborateur->getPrenom() . ' ' . $collaborateur->getNom(),
                    'data' => []
                ], 200);
            }
            
            return $this->json([
                'message' => 'Disponibilité du collaborateur mise à jour automatiquement',
                'data' => $update
            ], 200);
            
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la mise à jour de la disponibilité : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les statistiques de charge pour un collaborateur
     */
    #[Route('/{id}/workload-stats', methods: ['GET'])]
    public function getWorkloadStats(Collaborateur $collaborateur): JsonResponse
    {
        try {
            // Utiliser le TaskAssignmentService pour calculer les stats
            $stats = $this->taskAssignmentService->getWorkloadStats($collaborateur);
            
            return $this->json([
                'message' => 'Statistiques de charge récupérées',
                'data' => $stats
            ], 200);
            
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors du calcul des statistiques : ' . $e->getMessage()
            ], 500);
        }
    }
}
