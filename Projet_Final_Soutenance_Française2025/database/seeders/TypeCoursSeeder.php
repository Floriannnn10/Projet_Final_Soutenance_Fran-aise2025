<?php

namespace Database\Seeders;

use App\Models\TypeCours;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeCoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typesCours = [
            ['nom' => TypeCours::PRESENTIEL],
            ['nom' => TypeCours::ELEARNING],
            ['nom' => TypeCours::WORKSHOP],
        ];

        foreach ($typesCours as $type) {
            TypeCours::firstOrCreate($type);
        }

        $this->command->info('✅ Types de cours créés avec succès !');
    }
}
