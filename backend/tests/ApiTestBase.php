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
        
        // Vérifier si les utilisateurs existent déjà
        $existingAdmin = $entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@test.com']);
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user@test.com']);
        
        if (!$existingAdmin) {
            // Créer l'admin
            $admin = new User();
            $admin->setEmail('admin@test.com');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($hasher->hashPassword($admin, 'secret123'));
            $entityManager->persist($admin);
        }
        
        if (!$existingUser) {
            // Créer l'user normal
            $user = new User();
            $user->setEmail('user@test.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($hasher->hashPassword($user, 'user123'));
            $entityManager->persist($user);
        }
        
        $entityManager->flush();
    }

    protected function jwt(string $email, string $password = 'Admin123!'): string
    {
        $client = static::getClient();
        
        $response = $client->request('POST', '/api/login', [
            'json' => [
                'email' => $email, 
                'password' => $password
            ]
        ]);

        self::assertResponseIsSuccessful();
        $responseData = $response->toArray();
        return $responseData['token'];
    }
}
