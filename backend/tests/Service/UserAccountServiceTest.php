<?php

namespace App\Tests\Service;

use App\Service\UserAccountService;
use App\Entity\User;
use App\Entity\Collaborateur;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class UserAccountServiceTest extends TestCase
{
    private UserAccountService $service;
    
    /** @var EntityManagerInterface&MockObject */
    private $entityManager;
    
    /** @var UserPasswordHasherInterface&MockObject */
    private $passwordHasher;
    
    /** @var EntityRepository&MockObject */
    private $userRepository;

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

    public function testCreateOrUpdateUserAccountNewUser()
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('jean.dupont@example.com');
        $collaborateur->setRole('Manager');
        
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);
        
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'jean.dupont@example.com'])
            ->willReturn(null);
        
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_password');
        
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
        $this->assertEquals('hashed_password', $user->getPassword());
        $this->assertContains('ROLE_MANAGER', $user->getRoles());
        $this->assertTrue($user->getMustChangePassword());
        $this->assertTrue($user->getIsActive());
    }

    public function testCreateOrUpdateUserAccountExistingUser()
    {
        // Arrange
        $existingUser = new User();
        $existingUser->setEmail('jean.dupont@example.com');
        $existingUser->setPassword('old_password');
        $existingUser->setRoles(['ROLE_USER']);
        
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('jean.dupont@example.com');
        $collaborateur->setRole('Chef de projet');
        
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);
        
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'jean.dupont@example.com'])
            ->willReturn($existingUser);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $user = $this->service->createOrUpdateUserAccount($collaborateur);
        
        // Assert
        $this->assertSame($existingUser, $user);
        $this->assertEquals('jean.dupont@example.com', $user->getEmail());
        $this->assertContains('ROLE_CHEF_PROJET', $user->getRoles());
    }

    public function testMapCollaborateurRoleToUserRole()
    {
        // Test avec Chef de projet
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('chef@example.com');
        $collaborateur->setRole('Chef de projet');
        
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
        
        $user = $this->service->createOrUpdateUserAccount($collaborateur);
        
        $this->assertContains('ROLE_CHEF_PROJET', $user->getRoles());
    }

    public function testMapCollaborateurRoleToUserRoleManager()
    {
        // Test avec Manager
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('manager@example.com');
        $collaborateur->setRole('Manager');
        
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
        
        $user = $this->service->createOrUpdateUserAccount($collaborateur);
        
        $this->assertContains('ROLE_MANAGER', $user->getRoles());
    }

    public function testMapCollaborateurRoleToUserRoleDefault()
    {
        // Test avec Collaborateur (rôle par défaut)
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('collab@example.com');
        $collaborateur->setRole('Collaborateur');
        
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
        
        $user = $this->service->createOrUpdateUserAccount($collaborateur);
        
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testHasValidTemporaryAccess()
    {
        // Arrange
        $user = new User();
        $user->setMustChangePassword(true);
        $user->setExpiresAt(new \DateTime('+1 day'));
        
        // Act
        $result = $this->service->hasValidTemporaryAccess($user);
        
        // Assert
        $this->assertTrue($result);
    }

    public function testHasValidTemporaryAccessExpired()
    {
        // Arrange
        $user = new User();
        $user->setMustChangePassword(true);
        $user->setExpiresAt(new \DateTime('-1 day'));
        
        // Act
        $result = $this->service->hasValidTemporaryAccess($user);
        
        // Assert
        $this->assertFalse($result);
    }

    public function testHasValidTemporaryAccessPasswordChanged()
    {
        // Arrange
        $user = new User();
        $user->setMustChangePassword(false);
        $user->setExpiresAt(new \DateTime('+1 day'));
        
        // Act
        $result = $this->service->hasValidTemporaryAccess($user);
        
        // Assert
        $this->assertFalse($result);
    }

    public function testExtendTemporaryAccess()
    {
        // Arrange
        $user = new User();
        $user->setExpiresAt(new \DateTime());
        $user->setMustChangePassword(false);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $this->service->extendTemporaryAccess($user, 5);
        
        // Assert
        $this->assertTrue($user->getMustChangePassword());
        $this->assertGreaterThan(new \DateTime(), $user->getExpiresAt());
    }

    public function testDeactivateUser()
    {
        // Arrange
        $user = new User();
        $user->setIsActive(true);
        $user->setExpiresAt(new \DateTime('+1 day'));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $this->service->deactivateUser($user);
        
        // Assert
        $this->assertFalse($user->getIsActive());
        $this->assertLessThanOrEqual(new \DateTime(), $user->getExpiresAt());
    }

    public function testReactivateUser()
    {
        // Arrange
        $user = new User();
        $user->setIsActive(false);
        $user->setMustChangePassword(false);
        $user->setExpiresAt(new \DateTime('-1 day'));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $this->service->reactivateUser($user);
        
        // Assert
        $this->assertTrue($user->getIsActive());
        $this->assertTrue($user->getMustChangePassword());
        $this->assertGreaterThan(new \DateTime(), $user->getExpiresAt());
    }

    public function testCreateNewUserWithDefaultPassword()
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('newuser@example.com');
        $collaborateur->setRole('Collaborateur');
        
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->userRepository);
        
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
        
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), 'admin')
            ->willReturn('hashed_admin_password');
        
        $this->entityManager->expects($this->once())
            ->method('persist');
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $user = $this->service->createOrUpdateUserAccount($collaborateur);
        
        // Assert
        $this->assertEquals('hashed_admin_password', $user->getPassword());
        $this->assertTrue($user->getMustChangePassword());
    }

    public function testUpdateExistingUserWithDifferentEmail()
    {
        // Arrange
        $existingUser = new User();
        $existingUser->setEmail('old@example.com');
        $existingUser->setPassword('old_password');
        $existingUser->setRoles(['ROLE_USER']);
        
        $collaborateur = new Collaborateur();
        $collaborateur->setEmail('new@example.com');
        $collaborateur->setRole('Manager');
        
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->userRepository);
        
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($existingUser);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $user = $this->service->createOrUpdateUserAccount($collaborateur);
        
        // Assert
        $this->assertEquals('new@example.com', $user->getEmail());
        $this->assertContains('ROLE_MANAGER', $user->getRoles());
    }
}