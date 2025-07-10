<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DIRECT DE L'API ÉTUDIANTS ===\n\n";

// Simuler une requête HTTP
$request = new \Illuminate\Http\Request();
$request->headers->set('Accept', 'application/json');

// Créer une instance du contrôleur
$controller = new \App\Http\Controllers\Api\EtudiantController();

// Test 1: Récupérer l'étudiant 21
echo "=== TEST ÉTUDIANT 21 ===\n";
try {
    $etudiant = \App\Models\Etudiant::with(['user', 'classe', 'parents'])->find(21);
    if ($etudiant) {
        $resource = new \App\Http\Resources\EtudiantResource($etudiant);
        $data = $resource->toArray($request);
        
        echo "✅ Étudiant 21 trouvé:\n";
        echo "   - ID: {$data['id']}\n";
        echo "   - Nom: {$data['nom']}\n";
        echo "   - Prénom: {$data['prenom']}\n";
        echo "   - Nom complet: {$data['nom_complet']}\n";
        echo "   - Email: {$data['user']['email']}\n";
        echo "   - Classe: {$data['classe']['nom']}\n";
        
        echo "\n=== RÉPONSE COMPLÈTE ===\n";
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } else {
        echo "❌ Étudiant 21 non trouvé\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n\n=== TEST ÉTUDIANT 1 ===\n";
try {
    $etudiant = \App\Models\Etudiant::with(['user', 'classe', 'parents'])->find(1);
    if ($etudiant) {
        $resource = new \App\Http\Resources\EtudiantResource($etudiant);
        $data = $resource->toArray($request);
        
        echo "✅ Étudiant 1 trouvé:\n";
        echo "   - ID: {$data['id']}\n";
        echo "   - Nom: {$data['nom']}\n";
        echo "   - Prénom: {$data['prenom']}\n";
        echo "   - Nom complet: {$data['nom_complet']}\n";
        echo "   - Email: {$data['user']['email']}\n";
        echo "   - Classe: {$data['classe']['nom']}\n";
        
    } else {
        echo "❌ Étudiant 1 non trouvé\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
} 