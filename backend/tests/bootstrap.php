<?php

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\ORM\Tools\SchemaTool;
use App\Kernel;

// Charge le bootstrap Symfony
require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Démarre le kernel test
$kernel = new Kernel('test', true);
$kernel->boot();

$container = $kernel->getContainer();
$em = $container->get('doctrine')->getManager();

// (Re)crée le schéma Doctrine
$tool = new SchemaTool($em);
$metadata = $em->getMetadataFactory()->getAllMetadata();
$tool->dropDatabase();
$tool->createSchema($metadata);

// Seed d'un admin pour les tests JWT
try {
    /** @var \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $hasher */
    $hasher = $container->get('security.user_password_hasher');
    
    // Supprimer tous les utilisateurs existants pour éviter les doublons
    $existingUsers = $em->getRepository(\App\Entity\User::class)->findAll();
    foreach ($existingUsers as $user) {
        $em->remove($user);
    }
    $em->flush();
    
    // Créer l'admin
    $user = (new \App\Entity\User())
        ->setEmail('admin@test.com')
        ->setRoles(['ROLE_ADMIN']);
    $user->setPassword($hasher->hashPassword($user, 'secret123'));
    $em->persist($user);
    
    // Créer l'user normal
    $userNormal = (new \App\Entity\User())
        ->setEmail('user@test.com')
        ->setRoles(['ROLE_USER']);
    $userNormal->setPassword($hasher->hashPassword($userNormal, 'user123'));
    $em->persist($userNormal);
    
    $em->flush();
} catch (Exception $e) {
    // Ignore si les utilisateurs existent déjà
}
