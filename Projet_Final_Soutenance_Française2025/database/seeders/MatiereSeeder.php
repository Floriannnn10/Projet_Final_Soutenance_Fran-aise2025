<?php

namespace Database\Seeders;

use App\Models\Matiere;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MatiereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matieres = [
            ['nom' => 'Électronique'],
            ['nom' => 'Programmation Web'],
            ['nom' => 'Design UI/UX'],
            ['nom' => 'Mathématiques'],
            ['nom' => 'Anglais Technique'],
            ['nom' => 'Base de Données'],
            ['nom' => 'Systèmes d\'Information'],
            ['nom' => 'Communication'],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate($matiere);
        }

        $this->command->info('✅ Matières créées avec succès !');
    }
}
