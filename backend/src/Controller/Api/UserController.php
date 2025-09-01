<?php
// src/Controller/Api/UserController.php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\CollaborateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/api/users')]
class UserController extends AbstractController
{
    #[Route('/profile', methods: ['GET'])]
    public function getProfile(
        Request $request,
        UserRepository $userRepository,
        CollaborateurRepository $collaborateurRepository
    ): JsonResponse {
        try {
            // Récupérer l'utilisateur connecté depuis le token JWT
            /** @var User $user */
            $user = $this->getUser();
            if (!$user || !$user instanceof User) {
                return $this->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            // Récupérer les informations du collaborateur correspondant
            $collaborateur = $collaborateurRepository->findOneBy(['email' => $user->getEmail()]);
            
            $profileData = [
                'email' => $user->getEmail(),
                'nomComplet' => $collaborateur ? $collaborateur->getPrenom() . ' ' . $collaborateur->getNom() : 'N/A',
                'role' => $collaborateur ? $collaborateur->getRole() : 'Utilisateur',
                'createdAt' => $user->getCreatedAt() ? $user->getCreatedAt()->format('Y-m-d H:i:s') : null,
                'lastLogin' => null, // À implémenter si nécessaire
                'isActive' => $user->getIsActive(),
                'roles' => $user->getRoles()
            ];

            return $this->json([
                'message' => 'Profil récupéré avec succès',
                'data' => $profileData
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la récupération du profil : ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/change-password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            /** @var User $user */
            $user = $this->getUser();
            if (!$user || !$user instanceof User) {
                return $this->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            $data = json_decode($request->getContent(), true);
            $currentPassword = $data['currentPassword'] ?? null;
            $newPassword = $data['newPassword'] ?? null;

            if (!$currentPassword || !$newPassword) {
                return $this->json(['message' => 'Mot de passe actuel et nouveau mot de passe requis'], 400);
            }

            // Vérifier le mot de passe actuel
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                return $this->json(['message' => 'Mot de passe actuel incorrect'], 401);
            }

            // Vérifier la longueur du nouveau mot de passe
            if (strlen($newPassword) < 8) {
                return $this->json(['message' => 'Le nouveau mot de passe doit contenir au moins 8 caractères'], 400);
            }

            // Hasher et enregistrer le nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            
            // Marquer que le mot de passe a été changé
            $user->setMustChangePassword(false);

            $em->flush();

            return $this->json([
                'message' => 'Mot de passe changé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors du changement de mot de passe : ' . $e->getMessage()
            ], 500);
        }
    }
}
