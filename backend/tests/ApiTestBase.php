<?php
// tests/ApiTestBase.php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class ApiTestBase extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        static::createClient();
        $this->createTestUsers();
    }

    private function createTestUsers(): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $hasher = static::getContainer()->get(\Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface::class);
        
        // Supprimer les utilisateurs existants pour s'assurer qu'ils sont recréés avec les bons mots de passe
        $existingAdmin = $entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@test.com']);
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user@test.com']);
        
        if ($existingAdmin) {
            $entityManager->remove($existingAdmin);
        }
        if ($existingUser) {
            $entityManager->remove($existingUser);
        }
        $entityManager->flush();
        
        // Créer l'admin
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($hasher->hashPassword($admin, 'secret123'));
        $entityManager->persist($admin);
        
        // Créer l'user normal
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, 'user123'));
        $entityManager->persist($user);
        
        $entityManager->flush();
    }

    protected function jwt(string $email, string $password = null): string
    {
        // Définir le mot de passe par défaut selon l'email
        if ($password === null) {
            $password = $email === 'admin@test.com' ? 'secret123' : 'user123';
        }
        
        // Utiliser directement le service JWT au lieu de faire un appel HTTP
        $container = static::getContainer();
        $userRepository = $container->get(\App\Repository\UserRepository::class);
        $passwordHasher = $container->get(\Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface::class);
        $jwtManager = $container->get(\Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface::class);
        
        $user = $userRepository->findOneBy(['email' => $email]);
        
        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            throw new \Exception("Invalid credentials for $email");
        }
        
        return $jwtManager->create($user);
    }
}
