<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$etudiant = \App\Models\Etudiant::with(['user', 'classe'])->find(21);

if ($etudiant) {
    echo "Étudiant 21: {$etudiant->nom} {$etudiant->prenom}\n";
    echo "Email: {$etudiant->user->email}\n";
    echo "Classe: {$etudiant->classe->nom}\n";
    
    $resource = new \App\Http\Resources\EtudiantResource($etudiant);
    $data = $resource->toArray(new \Illuminate\Http\Request());
    
    echo "Ressource JSON:\n";
    echo json_encode($data, JSON_PRETTY_PRINT);
} else {
    echo "Étudiant 21 non trouvé\n";
} 