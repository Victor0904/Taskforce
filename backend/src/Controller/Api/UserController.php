<?php
// src/Controller/Api/UserController.php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;   // ← import manquant
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]          // toute la classe réservée aux admins
#[Route('/api/users')]
final class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $repo
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->repo->findAll());
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->get('serializer')->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );

        $this->em->persist($user);
        $this->em->flush();

        return $this->json($user, 201);
    }

    // … autres actions show / update / delete
}
