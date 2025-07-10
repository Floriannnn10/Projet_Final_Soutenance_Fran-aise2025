<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Récupérer l'utilisateur admin
$user = \App\Models\User::where('email', 'admin@ifran.fr')->first();

if ($user) {
    // Créer un token 
    $token = $user->createToken('test-token')->plainTextToken;
    
    echo "=== TOKEN D'AUTHENTIFICATION POUR POSTMAN ===\n";
    echo "Email: admin@ifran.fr\n";
    echo "Mot de passe: password\n";
    echo "Token: " . $token . "\n";
    echo "=============================================\n";
    echo "\n";
    echo "=== CONFIGURATION POSTMAN ===\n";
    echo "1. Ajoutez le header: Authorization: Bearer " . $token . "\n";
    echo "2. URL de base: http://localhost:8000/api\n";
    echo "3. Testez d'abord: GET /api/health\n";
    echo "======================\n";
} else {
    echo "Utilisateur admin non trouvé. Vérifiez que les seeders ont été exécutés.\n";
} 