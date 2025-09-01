<?php

namespace App\Controller\Auth;

use App\Repository\UserRepository;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

#[AsController]
final class LoginController
{
    public function __construct() {}

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function __invoke(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['message' => 'Email et mot de passe requis.'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Identifiants invalides.'], 401);
        }

        // Vérifier si le compte est actif
        // if (!$user->getIsActive()) {
        //     return new JsonResponse(['message' => 'Compte désactivé. Contactez l\'administrateur.'], 403);
        // }

        // Vérifier si le compte a expiré
        // if ($user->isExpired()) {
        //     return new JsonResponse(['message' => 'Accès temporaire expiré. Contactez l\'administrateur.'], 403);
        // }

        // Créer ou supprimer les alertes selon le statut du mot de passe
        // if ($password === 'admin' || $user->getMustChangePassword()) {
        //     // Créer une alerte de changement de mot de passe
        //     if (!$this->userAlertService->hasPasswordChangeAlert($user)) {
        //         $this->userAlertService->createPasswordChangeAlert($user);
        //     }
        // } else {
        //     // Supprimer l'alerte si le mot de passe a été changé
        //     $this->userAlertService->removePasswordChangeAlert($user);
        // }

        // Créer une alerte d'accès temporaire si nécessaire
        // if ($user->isTemporaryAccess()) {
        //     if (!$this->userAlertService->hasTemporaryAccessAlert($user)) {
        //         $this->userAlertService->createTemporaryAccessAlert($user);
        //     }
        // }

        $token = $jwtManager->create($user);
        
        // Déterminer si le mot de passe doit être changé
        $mustChangePassword = $password === 'admin'; // Temporairement simplifié

        return new JsonResponse([
            'token' => $token,
            'mustChangePassword' => $mustChangePassword,
            'user' => [
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'expiresAt' => null, // Temporairement null
                'isTemporaryAccess' => false // Temporairement false
            ]
        ]);
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? 'admin';
        $roles = $data['roles'] ?? ['ROLE_USER'];

        if (!$email) {
            return new JsonResponse(['message' => 'Email requis'], 400);
        }
        // Doublon email → 409
        if ($userRepository->findOneBy(['email' => $email])) {
            return new JsonResponse(['message' => 'Email déjà utilisé'], 409);
        }
        if (!is_array($roles)) { $roles = ['ROLE_USER']; }
        $allowedRoles = ['ROLE_USER','ROLE_MANAGER','ROLE_CHEF_PROJET','ROLE_ADMIN'];
        $roles = array_values(array_unique(array_filter(array_map('strval', $roles))));
        $roles = array_intersect($roles, $allowedRoles);
        if (empty($roles)) { $roles = ['ROLE_USER']; }
        if (!in_array('ROLE_USER', $roles, true)) { $roles[] = 'ROLE_USER'; }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setRoles($roles);
        try {
            $em->persist($user);
            $em->flush();
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => 'Erreur lors de la création', 'error' => $e->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'Utilisateur créé'], 201);
    }
}
