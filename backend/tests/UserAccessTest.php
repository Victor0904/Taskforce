<?php
// tests/UserAccessTest.php
namespace App\Tests;

use App\Entity\User;

final class UserAccessTest extends ApiTestBase
{
    public function testAdminCanCreateUser(): void
    {
        $token = $this->jwt('admin@test.com', 'secret123');

        static::getClient()->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => [
                'email'         => 'new@test.local',
                'plainPassword' => 'Pass1234!'
            ]
        ]);

        self::assertResponseStatusCodeSame(201);
    }

    public function testUserCannotCreateUser(): void
    {
        // on suppose qu'un simple user existe (fixtures)
        $token = $this->jwt('user@test.com', 'user123');

        static::getClient()->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => "Bearer $token"
            ],
            'json' => [
                'email' => 'hack@test.local', 
                'plainPassword' => 'Hack1234!'
            ]
        ]);

        self::assertResponseStatusCodeSame(403);
    }
}
