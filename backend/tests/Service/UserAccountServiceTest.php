<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Entity\Collaborateur;
use App\Service\UserAccountService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAccountServiceTest extends TestCase
{
    private UserAccountService $service;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|UserPasswordHasherInterface $passwordHasher;
    private MockObject|EntityRepository $userRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRepository = $this->createMock(EntityRepository::class);

        $this->service = new UserAccountService(
            $this->entityManager,
            $this->passwordHasher
        );
    }

    public function testCreateNewUserAccount(): void
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('jean.dupont@example.com');
        $collaborateur->setRole('Chef de projet');
        $collaborateur->setPrenom('Jean');
        $collaborateur->setNom('Dupont');

        // Mock pour vérifier qu'aucun utilisateur n'existe
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'jean.dupont@example.com'])
            ->willReturn(null);

        // Mock pour le hashage du mot de passe
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_password');

        // Mock pour la persistance
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $user = $this->service->createOrUpdateUserAccount($collaborateur);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('jean.dupont@example.com', $user->getEmail());
        $this->assertEquals(['ROLE_CHEF_PROJET', 'ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getMustChangePassword());
        $this->assertTrue($user->getIsActive());
        $this->assertInstanceOf(\DateTime::class, $user->getExpiresAt());
    }

    public function testUpdateExistingUserAccount(): void
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('marie.martin@example.com');
        $collaborateur->setRole('Manager');

        $existingUser = new User();
        $existingUser->setEmail('marie.martin@example.com');
        $existingUser->setRoles(['ROLE_USER']);
        $existingUser->setMustChangePassword(true);

        // Mock pour trouver l'utilisateur existant
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'marie.martin@example.com'])
            ->willReturn($existingUser);

        // Mock pour la sauvegarde
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $user = $this->service->createOrUpdateUserAccount($collaborateur);

        // Assert
        $this->assertSame($existingUser, $user);
        $this->assertEquals(['ROLE_MANAGER', 'ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getMustChangePassword());
    }

    public function testMapCollaborateurRoleToUserRole(): void
    {
        // Test avec un seul rôle pour éviter les problèmes de mock
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('test@example.com');
        $collaborateur->setRole('Chef de projet');

        // Mock pour simuler un nouvel utilisateur
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_password');

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $user = $this->service->createOrUpdateUserAccount($collaborateur);

        // Assert
        $this->assertEquals(['ROLE_CHEF_PROJET', 'ROLE_USER'], $user->getRoles());
    }

    public function testHasValidTemporaryAccess(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setExpiresAt((new \DateTime())->modify('+1 day'));
        $user->setMustChangePassword(true);

        // Act
        $hasAccess = $this->service->hasValidTemporaryAccess($user);

        // Assert
        $this->assertTrue($hasAccess);
    }

    public function testHasInvalidTemporaryAccess(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setExpiresAt((new \DateTime())->modify('-1 day')); // Expiré
        $user->setMustChangePassword(true);

        // Act
        $hasAccess = $this->service->hasValidTemporaryAccess($user);

        // Assert
        $this->assertFalse($hasAccess);
    }

    public function testExtendTemporaryAccess(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setExpiresAt(new \DateTime());
        $user->setMustChangePassword(false);

        // Mock pour la sauvegarde
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $this->service->extendTemporaryAccess($user, 5);

        // Assert
        $this->assertTrue($user->getMustChangePassword());
        $expectedExpiry = (new \DateTime())->modify('+5 days');
        $this->assertEqualsWithDelta($expectedExpiry->getTimestamp(), $user->getExpiresAt()->getTimestamp(), 5);
    }

    public function testDeactivateUser(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setIsActive(true);
        $user->setExpiresAt((new \DateTime())->modify('+1 day'));

        // Mock pour la sauvegarde
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $this->service->deactivateUser($user);

        // Assert
        $this->assertFalse($user->getIsActive());
        $this->assertLessThanOrEqual((new \DateTime())->getTimestamp(), $user->getExpiresAt()->getTimestamp());
    }

    public function testReactivateUser(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setIsActive(false);
        $user->setMustChangePassword(false);
        $user->setExpiresAt(new \DateTime());

        // Mock pour la sauvegarde
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $this->service->reactivateUser($user);

        // Assert
        $this->assertTrue($user->getIsActive());
        $this->assertTrue($user->getMustChangePassword());
        $expectedExpiry = (new \DateTime())->modify('+3 days');
        $this->assertEqualsWithDelta($expectedExpiry->getTimestamp(), $user->getExpiresAt()->getTimestamp(), 5);
    }
}
