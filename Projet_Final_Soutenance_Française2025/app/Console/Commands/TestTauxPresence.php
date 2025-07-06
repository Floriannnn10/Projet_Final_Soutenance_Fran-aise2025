<?php

namespace App\Console\Commands;

use App\Models\Etudiant;
use Illuminate\Console\Command;

class TestTauxPresence extends Command
{
    protected $signature = 'test:taux-presence';
    protected $description = 'Tester la logique de calcul de taux de présence';

    public function handle(): int
    {
        $this->info('=== TEST NOUVELLE LOGIQUE DE DÉTECTION ===');

        $etudiant = Etudiant::first();
        if (!$etudiant) { 
            $this->error('Aucun étudiant trouvé');
            return self::FAILURE;
        }

        $tauxGlobal = $etudiant->calculerTauxPresenceGlobal();
        $estDroppe = $etudiant->estDroppeGlobal();
        $necessiteAssistance = $etudiant->necessiteAssistanceGlobal();

        $this->info("Étudiant: {$etudiant->nom_complet}");
        $this->info("Taux global: {$tauxGlobal}%");
        $this->info("Est droppé (≤25%): " . ($estDroppe ? 'OUI' : 'NON'));
        $this->info("Nécessite assistance (25-30%): " . ($necessiteAssistance ? 'OUI' : 'NON'));

        if ($tauxGlobal > 25 && $tauxGlobal <= 30) {
            $this->warn("Résultat attendu: {$etudiant->nom} devrait être en ASSISTANCE (taux 25-30%)");
        } elseif ($tauxGlobal <= 25) {
            $this->error("Résultat attendu: {$etudiant->nom} devrait être DROPPÉ (taux ≤25%)");
        } else {
            $this->info("Résultat attendu: {$etudiant->nom} devrait être OK (taux >30%)");
        }

        return self::SUCCESS;
    }
}
