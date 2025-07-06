<?php
// src/Controller/Auth/LoginController.php
namespace App\Controller\Auth;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class LoginController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        // Si on arrive ici, c’est que le firewall n’a pas intercepté
        return new JsonResponse(['message' => 'Identifiants invalides.'], 401);
    }
}
