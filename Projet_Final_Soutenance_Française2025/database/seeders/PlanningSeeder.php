<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Planning;
use App\Models\TypeCours;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::all();
        $typesCours = TypeCours::all();

        // Créer des plannings pour les 2 prochaines semaines
        $dateDebut = Carbon::now()->startOfWeek();

        for ($semaine = 0; $semaine < 2; $semaine++) {
            $semaineDate = $dateDebut->copy()->addWeeks($semaine);

            foreach ($classes as $classe) {
                // Créer 3-5 cours par classe par semaine
                $nombreCours = rand(3, 5);

                for ($i = 0; $i < $nombreCours; $i++) {
                    // Choisir un jour aléatoire de la semaine (lundi à vendredi)
                    $jour = rand(1, 5); // 1 = lundi, 5 = vendredi
                    $date = $semaineDate->copy()->addDays($jour - 1);

                    // Choisir une heure de début aléatoire (8h à 17h)
                    $heureDebut = rand(8, 17);
                    $minuteDebut = rand(0, 1) * 30; // 0 ou 30 minutes

                    // Durée du cours : 1h30 ou 2h
                    $duree = rand(1, 2) * 60 + 30; // 90 ou 150 minutes

                    $heureFin = Carbon::createFromTime($heureDebut, $minuteDebut)->addMinutes($duree);

                    // Choisir aléatoirement matière, enseignant et type de cours
                    $matiere = $matieres->random();
                    $enseignant = $enseignants->random();
                    $typeCours = $typesCours->random();

                    Planning::create([
                        'classe_id' => $classe->id,
                        'matiere_id' => $matiere->id,
                        'enseignant_id' => $enseignant->id,
                        'type_cours_id' => $typeCours->id,
                        'date' => $date->toDateString(),
                        'heure_debut' => Carbon::createFromTime($heureDebut, $minuteDebut),
                        'heure_fin' => $heureFin,
                        'is_annule' => false,
                    ]);
                }
            }
        }

        $this->command->info('✅ Plannings créés avec succès !');
    }
}
