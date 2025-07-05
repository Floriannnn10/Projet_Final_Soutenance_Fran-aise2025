<?php

namespace Database\Seeders;

use App\Models\Etudiant;
use App\Models\ParentUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentEtudiantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $etudiants = Etudiant::all();
        $parents = ParentUser::all();

        // On suppose qu'il y a au moins autant de parents que nécessaire
        foreach ($etudiants as $index => $etudiant) {
            $parent = $parents[$index % $parents->count()];
            DB::table('parent_etudiant')->insert([
                'parent_id' => $parent->id,
                'etudiant_id' => $etudiant->id,
            ]);
        }

        $this->command->info('✅ Liens parent-étudiant créés avec succès !');
    }
}
