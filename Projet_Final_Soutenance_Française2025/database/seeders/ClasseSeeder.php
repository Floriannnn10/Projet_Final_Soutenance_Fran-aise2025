<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['nom' => 'Prépa 1'],
            ['nom' => 'Prépa 2'],
            ['nom' => 'B1 Développement'],
            ['nom' => 'B2 Développement'],
            ['nom' => 'B3 Développement'],
            ['nom' => 'B1 Design'],
            ['nom' => 'B2 Design'],
        ];

        foreach ($classes as $classe) {
            Classe::firstOrCreate($classe);
        }

        $this->command->info('✅ Classes créées avec succès !');
    }
}
