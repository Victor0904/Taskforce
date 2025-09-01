<?php

/**
 * Script pour exécuter tous les tests PHPUnit avec rapport détaillé
 */

echo "=== EXÉCUTION DES TESTS UNITAIRES PHPUNIT ===\n\n";

// Configuration
$phpunitPath = 'vendor/bin/phpunit';
$testDirectories = [
    'tests/Service/',
    'tests/Entity/',
    'tests/Controller/',
    'tests/Repository/'
];

$totalTests = 0;
$totalAssertions = 0;
$totalFailures = 0;
$totalErrors = 0;
$totalSkipped = 0;

echo "📁 Répertoires de tests à exécuter :\n";
foreach ($testDirectories as $dir) {
    echo "   - $dir\n";
}
echo "\n";

// Exécuter les tests pour chaque répertoire
foreach ($testDirectories as $dir) {
    echo "🧪 Exécution des tests dans $dir\n";
    echo str_repeat("-", 50) . "\n";
    
    $command = "php $phpunitPath $dir --verbose";
    $output = [];
    $returnCode = 0;
    
    exec($command, $output, $returnCode);
    
    // Afficher la sortie
    foreach ($output as $line) {
        echo $line . "\n";
    }
    
    // Parser les résultats
    $outputText = implode("\n", $output);
    if (preg_match('/Tests: (\d+), Assertions: (\d+)(?:, Failures: (\d+))?(?:, Errors: (\d+))?(?:, Skipped: (\d+))?/', $outputText, $matches)) {
        $totalTests += (int)$matches[1];
        $totalAssertions += (int)$matches[2];
        $totalFailures += (int)($matches[3] ?? 0);
        $totalErrors += (int)($matches[4] ?? 0);
        $totalSkipped += (int)($matches[5] ?? 0);
    }
    
    echo "\n";
}

// Résumé final
echo "📊 RÉSUMÉ FINAL\n";
echo str_repeat("=", 50) . "\n";
echo "Total des tests : $totalTests\n";
echo "Total des assertions : $totalAssertions\n";
echo "Échecs : $totalFailures\n";
echo "Erreurs : $totalErrors\n";
echo "Tests ignorés : $totalSkipped\n";

$successRate = $totalTests > 0 ? (($totalTests - $totalFailures - $totalErrors) / $totalTests) * 100 : 0;
echo "Taux de réussite : " . number_format($successRate, 1) . "%\n";

if ($totalFailures === 0 && $totalErrors === 0) {
    echo "\n✅ Tous les tests sont passés avec succès !\n";
} else {
    echo "\n❌ Certains tests ont échoué.\n";
}

echo "\n=== FIN DU RAPPORT ===\n";
