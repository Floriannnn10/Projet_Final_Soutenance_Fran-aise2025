<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DES ROUTES API ===\n\n";

// Test 1: Vérifier que la base de données est accessible
try {
    $etudiants = \App\Models\Etudiant::count();
    echo "✅ Base de données accessible - {$etudiants} étudiants trouvés\n";
} catch (Exception $e) {
    echo "❌ Erreur base de données: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Vérifier l'utilisateur admin
try {
    $admin = \App\Models\User::where('email', 'admin@ifran.fr')->first();
    if ($admin) {
        echo "✅ Utilisateur admin trouvé: {$admin->nom_complet}\n";
    } else {
        echo "❌ Utilisateur admin non trouvé\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Erreur utilisateur admin: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Générer un token
try {
    $token = $admin->createToken('test-token')->plainTextToken;
    echo "✅ Token généré: " . substr($token, 0, 20) . "...\n";
} catch (Exception $e) {
    echo "❌ Erreur génération token: " . $e->getMessage() . "\n";
    exit(1);
} 

// Test 4: Vérifier les modèles
try {
    $classes = \App\Models\Classe::count();
    $matieres = \App\Models\Matiere::count();
    $presences = \App\Models\Presence::count();
    $justifications = \App\Models\JustificationAbsence::count();
    
    echo "✅ Modèles accessibles:\n";
    echo "   - Classes: {$classes}\n";
    echo "   - Matières: {$matieres}\n";
    echo "   - Présences: {$presences}\n";
    echo "   - Justifications: {$justifications}\n";
} catch (Exception $e) {
    echo "❌ Erreur modèles: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Vérifier les relations
try {
    $etudiant = \App\Models\Etudiant::with(['user', 'classe', 'presences'])->first();
    if ($etudiant) {
        echo "✅ Relations Eloquent fonctionnelles:\n";
        echo "   - Étudiant: {$etudiant->nom_complet}\n";
        echo "   - Classe: {$etudiant->classe->nom}\n";
        echo "   - Présences: " . $etudiant->presences->count() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur relations: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 6: Vérifier les calculs de taux de présence
try {
    $taux = $etudiant->calculerTauxPresenceGlobal();
    echo "✅ Calcul taux de présence: {$taux}%\n";
} catch (Exception $e) {
    echo "❌ Erreur calcul taux: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== RÉSUMÉ ===\n";
echo "🎉 Tous les tests sont passés avec succès !\n";
echo "📋 Token pour Postman: {$token}\n";
echo "🌐 URL de base: http://localhost:8000/api\n";
echo "📖 Consultez POSTMAN_TESTING_GUIDE.md pour les instructions détaillées\n"; 