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

// Les utilisateurs de test seront créés dans ApiTestBase
