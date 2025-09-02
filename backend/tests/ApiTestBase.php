<?php
// tests/ApiTestBase.php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class ApiTestBase extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        static::createClient();
    }

    protected function jwt(string $email, string $password = 'Admin123!'): string
    {
        $client = static::getClient();
        $response = $client->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email, 
                'password' => $password
            ]
        ]);

        self::assertResponseIsSuccessful();
        return json_decode($response->getContent(), true)['token'];
    }
}
