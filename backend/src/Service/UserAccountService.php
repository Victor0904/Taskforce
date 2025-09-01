<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Collaborateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserAccountService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * Crée ou met à jour un compte utilisateur pour un collaborateur
     */
    public function createOrUpdateUserAccount(Collaborateur $collaborateur): User
    {
        // Vérifier si un utilisateur existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $collaborateur->getEmail()]);

        if ($existingUser) {
            // Mettre à jour l'utilisateur existant
            return $this->updateExistingUser($existingUser, $collaborateur);
        } else {
            // Créer un nouvel utilisateur
            return $this->createNewUser($collaborateur);
        }
    }

    /**
     * Crée un nouvel utilisateur
     */
    private function createNewUser(Collaborateur $collaborateur): User
    {
        $user = new User();
        $user->setEmail($collaborateur->getEmail());
        $user->setPassword($this->passwordHasher->hashPassword($user, 'admin'));
        
        // Définir les rôles selon le rôle du collaborateur
        $roles = $this->mapCollaborateurRoleToUserRole($collaborateur->getRole());
        $user->setRoles($roles);
        
        // Configuration des accès temporaires
        $user->setMustChangePassword(true);
        $user->setExpiresAt((new \DateTime())->modify('+3 days'));
        $user->setIsActive(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        

        
        return $user;
    }

    /**
     * Met à jour un utilisateur existant
     */
    private function updateExistingUser(User $user, Collaborateur $collaborateur): User
    {
        // Mettre à jour l'email si nécessaire
        if ($user->getEmail() !== $collaborateur->getEmail()) {
            $user->setEmail($collaborateur->getEmail());
        }

        // Mettre à jour les rôles
        $roles = $this->mapCollaborateurRoleToUserRole($collaborateur->getRole());
        $user->setRoles($roles);

        // Réinitialiser l'accès temporaire si le mot de passe n'a pas été changé
        if ($user->getMustChangePassword()) {
            $user->setExpiresAt((new \DateTime())->modify('+3 days'));

        }

        $this->entityManager->flush();

        return $user;
    }

    /**
     * Mappe le rôle du collaborateur vers les rôles utilisateur
     */
    private function mapCollaborateurRoleToUserRole(string $collaborateurRole): array
    {
        return match ($collaborateurRole) {
            'Chef de projet' => ['ROLE_CHEF_PROJET'],
            'Manager' => ['ROLE_MANAGER'],
            'Collaborateur' => ['ROLE_USER'],
            default => ['ROLE_USER']
        };
    }

    /**
     * Vérifie si un utilisateur a un accès temporaire valide
     */
    public function hasValidTemporaryAccess(User $user): bool
    {
        return $user->isTemporaryAccess();
    }

    /**
     * Prolonge l'accès temporaire d'un utilisateur
     */
    public function extendTemporaryAccess(User $user, int $days = 3): void
    {
        $user->setExpiresAt((new \DateTime())->modify("+{$days} days"));
        $user->setMustChangePassword(true);
        
        $this->entityManager->flush();
    }

    /**
     * Désactive un compte utilisateur
     */
    public function deactivateUser(User $user): void
    {
        $user->setIsActive(false);
        $user->setExpiresAt(new \DateTime());
        
        $this->entityManager->flush();
    }

    /**
     * Réactive un compte utilisateur avec un nouvel accès temporaire
     */
    public function reactivateUser(User $user): void
    {
        $user->setIsActive(true);
        $user->setMustChangePassword(true);
        $user->setExpiresAt((new \DateTime())->modify('+3 days'));
        
        $this->entityManager->flush();
    }
}
