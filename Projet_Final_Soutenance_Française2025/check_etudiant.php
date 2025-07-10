<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

function is_bcrypt($hash) {
    return (strlen($hash) === 60 && substr($hash, 0, 4) === '$2y$');
}

$users = User::all();
$count = 0;
foreach ($users as $user) {
    if (!is_bcrypt($user->password)) {
        $user->password = bcrypt($user->password);
        $user->save();
        $count++;
        echo "Mot de passe re-hashé pour l'utilisateur ID {$user->id} ({$user->email})\n";
    }
}

echo "\n$total utilisateurs corrigés : $count\n"; 