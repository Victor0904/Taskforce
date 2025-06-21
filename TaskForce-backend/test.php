<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Kernel;

// Si ça ne jette pas d'erreur, c'est que tout est ok
$kernel = new Kernel('dev', true);
echo "✅ Kernel OK\n";
