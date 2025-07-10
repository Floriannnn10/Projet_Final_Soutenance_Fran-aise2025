<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST RESSOURCE ÉTUDIANT 21 ===\n\n";

// Récupérer l'étudiant 21 avec ses relations
$etudiant = \App\Models\Etudiant::with(['user', 'classe', 'parents'])->find(21);

if ($etudiant) {
    echo "✅ Étudiant 21 trouvé:\n";
    echo "   - ID: {$etudiant->id}\n";
    echo "   - Nom: {$etudiant->nom}\n";
    echo "   - Prénom: {$etudiant->prenom}\n";
    echo "   - Nom complet: {$etudiant->nom_complet}\n";
    echo "   - Classe: {$etudiant->classe->nom}\n";
    echo "   - Email: {$etudiant->user->email}\n";
    
    // Tester la ressource
    $resource = new \App\Http\Resources\EtudiantResource($etudiant);
    $data = $resource->toArray(new \Illuminate\Http\Request());
    
    echo "\n=== DONNÉES DE LA RESSOURCE ===\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} else {
    echo "❌ Étudiant 21 non trouvé\n";
} 