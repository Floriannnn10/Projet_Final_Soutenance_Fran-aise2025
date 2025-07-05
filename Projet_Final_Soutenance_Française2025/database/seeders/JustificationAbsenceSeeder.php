<?php

namespace Database\Seeders;

use App\Models\Etudiant;
use App\Models\JustificationAbsence;
use App\Models\Presence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JustificationAbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les présences avec statut "absent"
        $absences = Presence::where('statut', 'absent')->get();

        if ($absences->isEmpty()) {
            $this->command->info('⚠️  Aucune absence trouvée pour créer des justifications');
            return;
        }

        $motifs = [
            'Maladie avec certificat médical',
            'Rendez-vous médical',
            'Problème de transport',
            'Raison familiale',
            'Événement sportif',
            'Formation externe',
            'Problème technique (e-learning)',
            'Absence justifiée par les parents',
            'Autre',
        ];

        $coordinateurs = User::whereHas('role', function($query) {
            $query->where('nom', 'Coordinateur');
        })->get();

        $justificationsCreees = 0;
        $absencesTraitees = $absences->shuffle(); // Mélanger pour plus de variété

        // Garantir au moins 2 justifications si on a assez d'absences
        $justificationsGaranties = min(2, $absences->count());

        foreach ($absencesTraitees as $index => $absence) {
            $doitCreerJustification = false;

            // Garantir les premières justifications
            if ($index < $justificationsGaranties) {
                $doitCreerJustification = true;
            } else {
                // 50% de chance pour les autres absences
                $doitCreerJustification = rand(1, 100) <= 50;
            }

            if ($doitCreerJustification) {
                $motif = $motifs[array_rand($motifs)];
                $dateJustification = Carbon::parse($absence->created_at)->addDays(rand(1, 3));
                $coordinateur = $coordinateurs->random();

                JustificationAbsence::create([
                    'presence_id' => $absence->id,
                    'justifie_par_user_id' => $coordinateur->id,
                    'date_justification' => $dateJustification->toDateString(),
                    'motif' => $motif,
                ]);

                // Marquer la présence comme justifiée
                $absence->update(['is_justifie' => true]);

                $justificationsCreees++;
            }
        }

        $this->command->info("✅ {$justificationsCreees} justifications d'absence créées avec succès !");
        $this->command->info("📊 Basées sur {$absences->count()} absences trouvées");
        $this->command->info("🎯 Taux de justification : " . round(($justificationsCreees / $absences->count()) * 100, 1) . "%");
    }
}
