<?php
// tests/UserAccessTest.php
namespace App\Tests;

use App\Entity\User;

final class UserAccessTest extends ApiTestBase
{
    public function testAdminCanCreateUser(): void
    {
        $token = $this->jwt('admin@test.local');

        static::getClient()->jsonRequest('POST', '/api/users', [
            'email'         => 'new@test.local',
            'plainPassword' => 'Pass1234!'
        ], [
            'HTTP_AUTHORIZATION' => "Bearer $token"
        ]);

        self::assertResponseStatusCodeSame(201);
    }

    public function testUserCannotCreateUser(): void
    {
        // on suppose qu'un simple user existe (fixtures)
        $token = $this->jwt('user@test.local', 'User123!');

        static::getClient()->jsonRequest('POST', '/api/users', [
            'email' => 'hack@test.local', 
            'plainPassword' => 'Hack1234!'
        ], [
            'HTTP_AUTHORIZATION' => "Bearer $token"
        ]);

        self::assertResponseStatusCodeSame(403);
    }
}
