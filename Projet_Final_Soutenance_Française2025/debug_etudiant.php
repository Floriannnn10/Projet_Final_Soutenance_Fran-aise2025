<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DÉBOGAGE ÉTUDIANT ===\n\n";

// 1. Vérifier le nombre d'étudiants
$total = \App\Models\Etudiant::count();
echo "Nombre total d'étudiants: {$total}\n";

// 2. Vérifier l'étudiant 21 directement
$etudiant = \App\Models\Etudiant::find(21);
if ($etudiant) {
    echo "✅ Étudiant 21 trouvé directement:\n";
    echo "   - ID: {$etudiant->id}\n";
    echo "   - Nom: {$etudiant->nom}\n";
    echo "   - Prénom: {$etudiant->prenom}\n";
    echo "   - Classe ID: {$etudiant->classe_id}\n";
    echo "   - User ID: {$etudiant->user_id}\n";
} else {
    echo "❌ Étudiant 21 non trouvé directement\n";
}

// 3. Vérifier avec route model binding
try {
    $etudiantRoute = \App\Models\Etudiant::findOrFail(21);
    echo "✅ Étudiant 21 trouvé avec findOrFail:\n";
    echo "   - ID: {$etudiantRoute->id}\n";
    echo "   - Nom: {$etudiantRoute->nom}\n";
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    echo "❌ Étudiant 21 non trouvé avec findOrFail\n";
}

// 4. Vérifier les IDs disponibles
echo "\n=== IDs des étudiants disponibles ===\n";
$ids = \App\Models\Etudiant::pluck('id')->toArray();
echo "IDs: " . implode(', ', $ids) . "\n";

// 5. Tester avec un ID valide
if (!empty($ids)) {
    $testId = $ids[0];
    echo "\n=== TEST AVEC ID {$testId} ===\n";
    $testEtudiant = \App\Models\Etudiant::with(['user', 'classe'])->find($testId);
    if ($testEtudiant) {
        echo "✅ Étudiant {$testId} trouvé:\n";
        echo "   - Nom: {$testEtudiant->nom}\n";
        echo "   - Prénom: {$testEtudiant->prenom}\n";
        echo "   - Classe: {$testEtudiant->classe->nom}\n";
        echo "   - Email: {$testEtudiant->user->email}\n";
        
        // Tester la ressource
        $request = new \Illuminate\Http\Request();
        $resource = new \App\Http\Resources\EtudiantResource($testEtudiant);
        $data = $resource->toArray($request);
        
        echo "\n=== RESSOURCE POUR ID {$testId} ===\n";
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
} 