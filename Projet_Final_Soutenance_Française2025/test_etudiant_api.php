<?php

require_once 'vendor/autoload.php';

use App\Models\Etudiant;
use App\Http\Resources\EtudiantResource;

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DE L'API ÉTUDIANT ===\n\n";

// 1. Vérifier qu'il y a des étudiants en base
$etudiants = Etudiant::all();
echo "Nombre d'étudiants en base : " . $etudiants->count() . "\n";

if ($etudiants->count() > 0) {
    $premierEtudiant = $etudiants->first();
    echo "Premier étudiant - ID: {$premierEtudiant->id}, Nom: {$premierEtudiant->nom}, Prénom: {$premierEtudiant->prenom}\n";
    
    // 2. Tester la ressource
    echo "\n=== TEST DE LA RESSOURCE ===\n";
    $etudiantAvecRelations = Etudiant::with(['user', 'classe'])->find($premierEtudiant->id);
    
    if ($etudiantAvecRelations) {
        $resource = new EtudiantResource($etudiantAvecRelations);
        $data = $resource->toArray(request());
        
        echo "Données de la ressource :\n";
        echo "- ID: " . ($data['id'] ?? 'NULL') . "\n";
        echo "- Nom: " . ($data['nom'] ?? 'NULL') . "\n";
        echo "- Prénom: " . ($data['prenom'] ?? 'NULL') . "\n";
        echo "- Nom complet: " . ($data['nom_complet'] ?? 'NULL') . "\n";
        echo "- Classe ID: " . ($data['classe_id'] ?? 'NULL') . "\n";
        echo "- User ID: " . ($data['user_id'] ?? 'NULL') . "\n";
        
        if (isset($data['user'])) {
            echo "- User Email: " . ($data['user']['email'] ?? 'NULL') . "\n";
            echo "- User Role: " . ($data['user']['role'] ?? 'NULL') . "\n";
        }
        
        if (isset($data['classe'])) {
            echo "- Classe Nom: " . ($data['classe']['nom'] ?? 'NULL') . "\n";
        }
    } else {
        echo "ERREUR: Impossible de récupérer l'étudiant avec les relations\n";
    }
} else {
    echo "AUCUN ÉTUDIANT TROUVÉ EN BASE\n";
}

echo "\n=== FIN DU TEST ===\n"; 