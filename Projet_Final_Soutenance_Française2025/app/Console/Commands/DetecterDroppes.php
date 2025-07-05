<?php

namespace App\Console\Commands;

use App\Jobs\DetecterEtudiantsDroppes;
use App\Models\Etudiant;
use App\Models\Matiere;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DetecterDroppes extends Command
{
    /**
     * Le nom et la signature de la commande console
     */
    protected $signature = 'ifran:detecter-droppes
                            {--job : Exécuter en arrière-plan via un job}
                            {--matiere= : Filtrer par matière spécifique}
                            {--classe= : Filtrer par classe spécifique}';

    /**
     * La description de la commande console
     */
    protected $description = 'Détecter automatiquement les étudiants "droppés" (taux ≤ 30%) et nécessitant une assistance (taux 30-40%)';

    /**
     * Exécuter la commande console
     */
    public function handle(): int
    {
        $this->info('🚀 Détection automatique des étudiants IFRAN');
        $this->newLine();

        // Option pour exécuter en arrière-plan
        if ($this->option('job')) {
            DetecterEtudiantsDroppes::dispatch();
            $this->info('✅ Job de détection envoyé en arrière-plan');
            $this->info('📋 Consultez les logs pour voir les résultats');
            return self::SUCCESS;
        }

        // Détection en temps réel
        $etudiantsDroppes = [];
        $etudiantsAssistance = [];
        $notificationsDroppes = 0;
        $notificationsAssistance = 0;

        // Filtres
        $matiereId = $this->option('matiere');
        $classeId = $this->option('classe');

        $query = Etudiant::with(['parents', 'classe']);

        if ($classeId) {
            $query->where('classe_id', $classeId);
        }

        $etudiants = $query->get();

        $this->info("📊 Analyse de {$etudiants->count()} étudiant(s)...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($etudiants->count());
        $progressBar->start();

        foreach ($etudiants as $etudiant) {
            $etudiantDroppe = false;
            $etudiantAssistance = false;
            $matieresDroppees = [];
            $matieresAssistance = [];
            $tauxGlobal = $etudiant->calculerTauxPresenceGlobal();

            // Vérifier le taux global uniquement
            if ($etudiant->estDroppeGlobal()) {
                $etudiantDroppe = true;
                $matieresDroppees[] = 'toutes les matières';

                if (!$matiereId) {
                    $etudiant->envoyerNotificationDroppe();
                    $notificationsDroppes++;
                }
            } elseif ($etudiant->necessiteAssistanceGlobal()) {
                $etudiantAssistance = true;
                $matieresAssistance[] = 'toutes les matières';

                if (!$matiereId) {
                    $etudiant->envoyerNotificationAssistance();
                    $notificationsAssistance++;
                }
            }

            // Vérifier par matière seulement si un filtre matière est spécifié
            if ($matiereId) {
                $matieres = Matiere::where('id', $matiereId)->get();

                foreach ($matieres as $matiere) {
                    if ($etudiant->estDroppe($matiere->id)) {
                        $matieresDroppees[] = $matiere->nom;
                        $etudiant->envoyerNotificationDroppe($matiere->id);
                        $notificationsDroppes++;
                    } elseif ($etudiant->necessiteAssistance($matiere->id)) {
                        $matieresAssistance[] = $matiere->nom;
                        $etudiant->envoyerNotificationAssistance($matiere->id);
                        $notificationsAssistance++;
                    }
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

            if ($etudiantAssistance) {
                $etudiantsAssistance[] = [
                    'etudiant' => $etudiant->nom_complet,
                    'classe' => $etudiant->classe->nom ?? 'N/A',
                    'taux_global' => $tauxGlobal,
                    'matieres_assistance' => $matieresAssistance
                ];
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Affichage des résultats
        $this->info('📋 RÉSULTATS DE LA DÉTECTION :');
        $this->newLine();

        // Étudiants droppés
        if (empty($etudiantsDroppes)) {
            $this->warn('✅ Aucun étudiant "droppé" détecté !');
        } else {
            $this->error("⚠️ " . count($etudiantsDroppes) . " étudiant(s) 'droppé(s)' détecté(s) (taux ≤ 30%) :");
            $this->newLine();

            foreach ($etudiantsDroppes as $index => $droppe) {
                $this->line(($index + 1) . ". <fg=red>{$droppe['etudiant']}</> ({$droppe['classe']})");
                $this->line("   📊 Taux global : <fg=yellow>{$droppe['taux_global']}%</>");
                $this->line("   📚 Matières droppées : <fg=red>" . implode(', ', $droppe['matieres_droppees']) . "</>");
                $this->line("   🎯 Conséquence : <fg=red>Reprendre l'année</>");
                $this->newLine();
            }
        }

        // Étudiants nécessitant une assistance
        if (empty($etudiantsAssistance)) {
            $this->warn('✅ Aucun étudiant nécessitant une assistance détecté !');
        } else {
            $this->warn("📚 " . count($etudiantsAssistance) . " étudiant(s) nécessitant une assistance (taux 30-40%) :");
            $this->newLine();

            foreach ($etudiantsAssistance as $index => $assistance) {
                $this->line(($index + 1) . ". <fg=yellow>{$assistance['etudiant']}</> ({$assistance['classe']})");
                $this->line("   📊 Taux global : <fg=yellow>{$assistance['taux_global']}%</>");
                $this->line("   📚 Matières nécessitant assistance : <fg=yellow>" . implode(', ', $assistance['matieres_assistance']) . "</>");
                $this->line("   🎯 Conséquence : <fg=yellow>Passer en classe supérieure avec suivi renforcé</>");
                $this->newLine();
            }
        }

        $this->info("📧 {$notificationsDroppes} notification(s) 'droppé' envoyée(s)");
        $this->info("📧 {$notificationsAssistance} notification(s) 'assistance' envoyée(s)");

        $this->newLine();
        $this->info('✅ Détection terminée avec succès !');

        return self::SUCCESS;
    }
}
