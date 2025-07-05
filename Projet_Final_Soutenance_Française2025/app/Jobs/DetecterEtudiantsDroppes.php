<?php

namespace App\Jobs;

use App\Models\Etudiant;
use App\Models\Matiere;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DetecterEtudiantsDroppes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    /**
     * Créer une nouvelle instance du job
     */
    public function __construct()
    {
        //
    }

    /**
     * Exécuter le job
     */
    public function handle(): void
    {
        Log::info('🚀 Début de la détection automatique des étudiants "droppés"');

        $etudiantsDroppes = [];
        $notificationsEnvoyees = 0;

        // Récupérer tous les étudiants
        $etudiants = Etudiant::with(['parents', 'classe'])->get();

        foreach ($etudiants as $etudiant) {
            $etudiantDroppe = false;
            $matieresDroppees = [];

            // Vérifier le taux global
            $tauxGlobal = $etudiant->calculerTauxPresenceGlobal();

            if ($etudiant->estDroppeGlobal()) {
                $etudiantDroppe = true;
                $matieresDroppees[] = 'toutes les matières';

                // Notifier automatiquement
                $etudiant->envoyerNotificationDroppe();
                $notificationsEnvoyees++;

                Log::info("⚠️ Étudiant {$etudiant->nom_complet} droppé globalement (taux: {$tauxGlobal}%)");
            }

            // Vérifier par matière
            $matieres = Matiere::all();

            foreach ($matieres as $matiere) {
                $tauxMatiere = $etudiant->calculerTauxPresenceMatiere($matiere->id);

                if ($etudiant->estDroppe($matiere->id)) {
                    $etudiantDroppe = true;
                    $matieresDroppees[] = $matiere->nom;

                    // Notifier automatiquement pour cette matière
                    $etudiant->envoyerNotificationDroppe($matiere->id);
                    $notificationsEnvoyees++;

                    Log::info("⚠️ Étudiant {$etudiant->nom_complet} droppé en {$matiere->nom} (taux: {$tauxMatiere}%)");
                }
            }

            if ($etudiantDroppe) {
                $etudiantsDroppes[] = [
                    'etudiant' => $etudiant->nom_complet,
                    'classe' => $etudiant->classe->nom ?? 'N/A',
                    'taux_global' => $tauxGlobal,
                    'matieres_droppees' => $matieresDroppees
                ];
            }
        }

        // Log du rapport final
        Log::info("📊 Rapport de détection 'droppé' :");
        Log::info("- Étudiants droppés : " . count($etudiantsDroppes));
        Log::info("- Notifications envoyées : " . $notificationsEnvoyees);

        if (!empty($etudiantsDroppes)) {
            Log::info("📋 Liste des étudiants droppés :");
            foreach ($etudiantsDroppes as $droppe) {
                Log::info("  • {$droppe['etudiant']} ({$droppe['classe']}) - Taux global: {$droppe['taux_global']}% - Matières: " . implode(', ', $droppe['matieres_droppees']));
            }
        }

        Log::info('✅ Détection automatique des étudiants "droppés" terminée');
    }

    /**
     * Gérer l'échec du job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ Échec de la détection automatique des étudiants "droppés"', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
