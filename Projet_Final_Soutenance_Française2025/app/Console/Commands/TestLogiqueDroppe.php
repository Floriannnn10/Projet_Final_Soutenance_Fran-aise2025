<?php

namespace App\Console\Commands;

use App\Models\Etudiant;
use Illuminate\Console\Command;

class TestLogiqueDroppe extends Command
{
    protected $signature = 'test:logique-droppe {--etudiant= : ID de l\'étudiant spécifique}';
    protected $description = 'Tester la logique de détection droppé/assistance';

    public function handle(): int
    {
        $this->info('🧪 TEST DE LA LOGIQUE DROPPÉ/ASSISTANCE');
        $this->newLine();

        $etudiantId = $this->option('etudiant');

        if ($etudiantId) {
            $etudiants = Etudiant::where('id', $etudiantId)->get();
        } else {
            $etudiants = Etudiant::take(5)->get();
        }

        if ($etudiants->isEmpty()) {
            $this->error('Aucun étudiant trouvé');
            return self::FAILURE;
        }

        foreach ($etudiants as $etudiant) {
            $this->testEtudiant($etudiant);
            $this->newLine();
        }

        $this->info('✅ Test terminé !');
        return self::SUCCESS;
    }

    private function testEtudiant(Etudiant $etudiant): void
    {
        $tauxGlobal = $etudiant->calculerTauxPresenceGlobal();
        $estDroppe = $etudiant->estDroppeGlobal();
        $necessiteAssistance = $etudiant->necessiteAssistanceGlobal();

        $this->info("👤 Étudiant: {$etudiant->nom_complet}");
        $this->info("📊 Taux global: {$tauxGlobal}%");
        $this->info("🔴 Est droppé (≤30%): " . ($estDroppe ? 'OUI' : 'NON'));
        $this->info("🟡 Nécessite assistance (30-40%): " . ($necessiteAssistance ? 'OUI' : 'NON'));

        // Vérification de la cohérence
        if ($tauxGlobal <= 30) {
            if (!$estDroppe) {
                $this->error("❌ ERREUR: Taux {$tauxGlobal}% ≤ 30% mais pas marqué comme droppé !");
            } else {
                $this->info("✅ OK: Taux {$tauxGlobal}% ≤ 30% → Correctement marqué comme droppé");
            }

            if ($necessiteAssistance) {
                $this->error("❌ ERREUR: Étudiant droppé mais marqué comme nécessitant assistance !");
            } else {
                $this->info("✅ OK: Étudiant droppé → Pas d'assistance (cohérent)");
            }
        } elseif ($tauxGlobal > 30 && $tauxGlobal <= 40) {
            if ($estDroppe) {
                $this->error("❌ ERREUR: Taux {$tauxGlobal}% > 30% mais marqué comme droppé !");
            } else {
                $this->info("✅ OK: Taux {$tauxGlobal}% > 30% → Pas droppé (correct)");
            }

            if (!$necessiteAssistance) {
                $this->error("❌ ERREUR: Taux {$tauxGlobal}% entre 30-40% mais pas marqué comme assistance !");
            } else {
                $this->info("✅ OK: Taux {$tauxGlobal}% entre 30-40% → Correctement marqué comme assistance");
            }
        } else {
            if ($estDroppe) {
                $this->error("❌ ERREUR: Taux {$tauxGlobal}% > 40% mais marqué comme droppé !");
            } else {
                $this->info("✅ OK: Taux {$tauxGlobal}% > 40% → Pas droppé (correct)");
            }

            if ($necessiteAssistance) {
                $this->error("❌ ERREUR: Taux {$tauxGlobal}% > 40% mais marqué comme assistance !");
            } else {
                $this->info("✅ OK: Taux {$tauxGlobal}% > 40% → Pas d'assistance (correct)");
            }
        }

        // Test par matière
        $this->info("📚 Test par matière:");
        $matieres = \App\Models\Matiere::take(3)->get();

        foreach ($matieres as $matiere) {
            $tauxMatiere = $etudiant->calculerTauxPresenceMatiere($matiere->id);
            $estDroppeMatiere = $etudiant->estDroppe($matiere->id);
            $necessiteAssistanceMatiere = $etudiant->necessiteAssistance($matiere->id);

            $this->line("   {$matiere->nom}: {$tauxMatiere}% - " .
                       ($estDroppeMatiere ? 'DROPPÉ' : ($necessiteAssistanceMatiere ? 'ASSISTANCE' : 'OK')));
        }
    }
}
