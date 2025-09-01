<?php

/**
 * Script de test d'intégration pour vérifier que le backend fonctionne
 * avec le frontend après les corrections
 */

echo "=== TEST D'INTÉGRATION FRONTEND-BACKEND ===\n\n";

// Configuration
$baseUrl = 'http://localhost:8000';
$apiEndpoints = [
    '/api/taches' => 'GET',
    '/api/collaborateurs' => 'GET',
    '/api/competences' => 'GET',
    '/api/missions' => 'GET',
];

echo "🔍 Test des endpoints API...\n";
echo "Base URL: $baseUrl\n\n";

$results = [];
$totalTests = count($apiEndpoints);
$passedTests = 0;

foreach ($apiEndpoints as $endpoint => $method) {
    echo "Testing $method $endpoint... ";
    
    $url = $baseUrl . $endpoint;
    
    // Configuration cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ ERREUR: $error\n";
        $results[$endpoint] = ['status' => 'error', 'message' => $error];
    } elseif ($httpCode >= 200 && $httpCode < 300) {
        echo "✅ OK ($httpCode)\n";
        $results[$endpoint] = ['status' => 'success', 'code' => $httpCode];
        $passedTests++;
    } else {
        echo "⚠️  HTTP $httpCode\n";
        $results[$endpoint] = ['status' => 'warning', 'code' => $httpCode];
    }
}

echo "\n📊 RÉSULTATS:\n";
echo str_repeat("-", 50) . "\n";
echo "Tests réussis: $passedTests/$totalTests\n";
echo "Taux de réussite: " . round(($passedTests / $totalTests) * 100, 1) . "%\n\n";

// Détails des résultats
foreach ($results as $endpoint => $result) {
    $status = $result['status'];
    $icon = $status === 'success' ? '✅' : ($status === 'warning' ? '⚠️' : '❌');
    echo "$icon $endpoint: ";
    
    if ($status === 'success') {
        echo "HTTP {$result['code']}\n";
    } elseif ($status === 'warning') {
        echo "HTTP {$result['code']}\n";
    } else {
        echo $result['message'] . "\n";
    }
}

echo "\n";

// Recommandations
if ($passedTests === $totalTests) {
    echo "🎉 Tous les endpoints API fonctionnent correctement !\n";
    echo "Le backend est prêt pour le frontend.\n";
} elseif ($passedTests > 0) {
    echo "⚠️  Certains endpoints ont des problèmes.\n";
    echo "Vérifiez que le serveur Symfony est démarré :\n";
    echo "  php -S localhost:8000 -t public/\n";
} else {
    echo "❌ Aucun endpoint ne fonctionne.\n";
    echo "Vérifiez que :\n";
    echo "  1. Le serveur Symfony est démarré\n";
    echo "  2. La base de données est configurée\n";
    echo "  3. Les migrations sont exécutées\n";
}

echo "\n=== FIN DU TEST ===\n";
