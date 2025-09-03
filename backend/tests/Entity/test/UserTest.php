<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testEmailSetterAndGetter()
    {
        $user = new User();
        $email = 'test@example.com';
        
        $user->setEmail($email);
        
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($email, $user->getUserIdentifier());
    }

    public function testRolesManagement()
    {
        $user = new User();
        
        // Test des rôles par défaut
        $this->assertContains('ROLE_USER', $user->getRoles());
        
        // Test ajout de rôle
        $user->addRole('ROLE_ADMIN');
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
        
        // Test suppression de rôle
        $user->removeRole('ROLE_ADMIN');
        $this->assertNotContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
        
        // Test définition de rôles multiples
        $user->setRoles(['ROLE_MANAGER', 'ROLE_CHEF_PROJET']);
        $this->assertContains('ROLE_MANAGER', $user->getRoles());
        $this->assertContains('ROLE_CHEF_PROJET', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testPasswordManagement()
    {
        $user = new User();
        $password = 'hashed_password_123';
        
        $user->setPassword($password);
        
        $this->assertEquals($password, $user->getPassword());
    }

    public function testTemporaryAccessManagement()
    {
        $user = new User();
        
        // Test accès temporaire par défaut
        $this->assertTrue($user->getMustChangePassword());
        $this->assertTrue($user->isTemporaryAccess());
        
        // Test désactivation de l'accès temporaire
        $user->setMustChangePassword(false);
        $this->assertFalse($user->getMustChangePassword());
        $this->assertFalse($user->isTemporaryAccess());
        
        // Test expiration
        $user->setExpiresAt(new \DateTime('-1 day'));
        $this->assertTrue($user->isExpired());
        $this->assertFalse($user->isTemporaryAccess());
    }

    public function testActiveStatus()
    {
        $user = new User();
        
        $this->assertTrue($user->getIsActive());
        
        $user->setIsActive(false);
        $this->assertFalse($user->getIsActive());
    }

    public function testCreatedAtAndExpiresAt()
    {
        $user = new User();
        $now = new \DateTime();
        $expiresAt = new \DateTime('+3 days');
        
        $user->setCreatedAt($now);
        $user->setExpiresAt($expiresAt);
        
        $this->assertEquals($now, $user->getCreatedAt());
        $this->assertEquals($expiresAt, $user->getExpiresAt());
    }

    public function testToString()
    {
        $user = new User();
        $email = 'test@example.com';
        
        $user->setEmail($email);
        
        $this->assertEquals($email, (string) $user);
    }

    public function testEraseCredentials()
    {
        $user = new User();
        
        // Cette méthode ne fait rien pour l'instant, mais on teste qu'elle ne lève pas d'exception
        $user->eraseCredentials();
        
        $this->assertTrue(true); // Si on arrive ici, pas d'exception
    }
}
