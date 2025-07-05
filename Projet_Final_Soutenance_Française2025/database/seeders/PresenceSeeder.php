<?php

namespace Database\Seeders;

use App\Models\Etudiant;
use App\Models\Planning;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plannings = Planning::where('date', '<=', Carbon::now()->toDateString())->get();
        $etudiants = Etudiant::all();

        // Profils d'étudiants avec différents taux de présence
        $profilsEtudiants = [
            // Étudiants assidus (taux > 70%)
            'assidu' => [
                'present' => 85,
                'retard' => 10,
                'absent' => 5,
                'justification_rate' => 80, // 80% des absences sont justifiées
            ],
            // Étudiants moyens (taux 50-70%)
            'moyen' => [
                'present' => 70,
                'retard' => 15,
                'absent' => 15,
                'justification_rate' => 60, // 60% des absences sont justifiées
            ],
            // Étudiants en difficulté (taux 30-50%)
            'difficulte' => [
                'present' => 50,
                'retard' => 20,
                'absent' => 30,
                'justification_rate' => 40, // 40% des absences sont justifiées
            ],
            // Étudiants droppés (taux < 30%)
            'droppe' => [
                'present' => 20,
                'retard' => 10,
                'absent' => 70,
                'justification_rate' => 20, // 20% des absences sont justifiées
            ],
            // Étudiants avec 0% de présence (toutes absences justifiées) - DROPPÉS
            'zero_justifie' => [
                'present' => 0,
                'retard' => 0,
                'absent' => 100,
                'justification_rate' => 100, // 100% des absences sont justifiées
            ],
            // Étudiants avec 0% de présence (absences non justifiées) - DROPPÉS
            'zero_non_justifie' => [
                'present' => 0,
                'retard' => 0,
                'absent' => 100,
                'justification_rate' => 0, // 0% des absences sont justifiées
            ],
        ];

        foreach ($plannings as $planning) {
            // Récupérer les étudiants de la classe du planning
            $etudiantsClasse = $etudiants->where('classe_id', $planning->classe_id);

            foreach ($etudiantsClasse as $index => $etudiant) {
                // Assigner un profil basé sur l'index de l'étudiant
                $profils = array_keys($profilsEtudiants);
                $profilIndex = $index % count($profils);
                $profil = $profilsEtudiants[$profils[$profilIndex]];

                // Générer le statut selon le profil
                $rand = rand(1, 100);
                if ($rand <= $profil['present']) {
                    $statut = 'present';
                } elseif ($rand <= $profil['present'] + $profil['retard']) {
                    $statut = 'retard';
                } else {
                    $statut = 'absent';
                }

                // Si présent ou en retard, enregistrer l'heure d'arrivée
                $enregistreLe = null;
                if ($statut !== 'absent') {
                    $retard = $statut === 'retard' ? rand(5, 30) : rand(0, 5);
                    $enregistreLe = Carbon::parse($planning->heure_debut)->addMinutes($retard);
                }

                // Déterminer si l'absence est justifiée
                $isJustifie = false;
                if ($statut === 'absent') {
                    $isJustifie = rand(1, 100) <= $profil['justification_rate'];
                }

                Presence::create([
                    'planning_id' => $planning->id,
                    'etudiant_id' => $etudiant->id,
                    'statut' => $statut,
                    'enregistre_le' => $enregistreLe,
                    'enregistre_par_user_id' => $planning->enseignant_id,
                    'is_justifie' => $isJustifie,
                ]);
            }
        }

        $this->command->info('✅ Présences créées avec succès !');
        $this->command->info('📊 Profils créés :');
        $this->command->info('   - Étudiants assidus (taux > 70%)');
        $this->command->info('   - Étudiants moyens (taux 50-70%)');
        $this->command->info('   - Étudiants en difficulté (taux 30-50%)');
        $this->command->info('   - Étudiants droppés (taux < 30%)');
        $this->command->info('   - Étudiants 0% avec absences justifiées (DROPPÉS)');
        $this->command->info('   - Étudiants 0% avec absences non justifiées (DROPPÉS)');
    }
}
