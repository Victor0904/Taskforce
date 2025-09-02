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
    
    // Vérifier si l'admin existe déjà
    $existingAdmin = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => 'admin@test.com']);
    if (!$existingAdmin) {
        $user = (new \App\Entity\User())
            ->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($hasher->hashPassword($user, 'secret123'));
        $em->persist($user);
    }
    
    // Vérifier si l'user normal existe déjà
    $existingUser = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => 'user@test.com']);
    if (!$existingUser) {
        $userNormal = (new \App\Entity\User())
            ->setEmail('user@test.com')
            ->setRoles(['ROLE_USER']);
        $userNormal->setPassword($hasher->hashPassword($userNormal, 'user123'));
        $em->persist($userNormal);
    }
    
    $em->flush();
} catch (Exception $e) {
    // Ignore si les utilisateurs existent déjà
}
