<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->loadEnv(dirname(__DIR__).'/.env');

// Bootstrap Symfony
$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

 // Récupérer l'EntityManager
 $entityManager = $container->get('doctrine')->getManager();
 
 // Créer un hasher de mots de passe
 $hasherFactory = new PasswordHasherFactory([
 User::class => ['algorithm' => 'auto']
 ]);
 $passwordHasher = $hasherFactory->getPasswordHasher(User::class);
 
 // Interface console simple
 $io = new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());
 
 try {
 // Vérifier les arguments de la ligne de commande
 if ($argc < 3) {
 $io->error('Usage: php create-user.php <email> <password> [role]');
 $io->note('Rôles disponibles: ROLE_USER, ROLE_CHEF_PROJET, ROLE_MANAGER, ROLE_ADMIN');
 $io->note('Exemple: php create-user.php chef@example.com MonMotDePasse123! ROLE_CHEF_PROJET');
 exit(1);
 }
 
 $email = $argv[1];
 $password = $argv[2];
 $role = $argv[3] ?? 'ROLE_USER';
 
 // Validation de l'email
 if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
 $io->error('Adresse email invalide.');
 exit(1);
 }
 
 // Validation du mot de passe
 if (strlen($password) < 8) {
 $io->error('Le mot de passe doit contenir au moins 8 caractères.');
 exit(1);
 }
 
 // Rôles autorisés
 $allowedRoles = ['ROLE_USER', 'ROLE_CHEF_PROJET', 'ROLE_MANAGER', 'ROLE_ADMIN'];
 if (!in_array($role, $allowedRoles)) {
 $io->error('Rôle invalide. Rôles autorisés: ' . implode(', ', $allowedRoles));
 exit(1);
 }
 
 // Vérifier si l'utilisateur existe déjà
 $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
 if ($existingUser) {
 $io->error("Un utilisateur avec l'email '$email' existe déjà.");
 exit(1);
 }
 
 // Créer le nouvel utilisateur
 $user = new User();
 $user->setEmail($email);
 $user->setRoles([$role]);
 $user->setPassword($passwordHasher->hash($password));
 $user->setIsActive(true);
 $user->setMustChangePassword(false); // L'utilisateur n'a pas besoin de changer son mot de passe
 
 // Persister en base de données
 $entityManager->persist($user);
 $entityManager->flush();
 
 $io->success("Utilisateur créé avec succès !");
 $io->table(
 ['Propriété', 'Valeur'],
 [
 ['ID', $user->getId()],
 ['Email', $user->getEmail()],
 ['Rôles', implode(', ', $user->getRoles())],
 ['Actif', $user->getIsActive() ? 'Oui' : 'Non'],
 ['Doit changer le mot de passe', $user->getMustChangePassword() ? 'Oui' : 'Non'],
 ['Date de création', $user->getCreatedAt()->format('Y-m-d H:i:s')],
 ['Expire le', $user->getExpiresAt() ? $user->getExpiresAt()->format('Y-m-d H:i:s') : 'Jamais']
 ]
 );
 
 } catch (\Exception $e) {
 $io->error('Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
 exit(1);
 }