<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nom' => Role::ADMIN],
            ['nom' => Role::COORDINATEUR],
            ['nom' => Role::ENSEIGNANT],
            ['nom' => Role::ETUDIANT],
            ['nom' => Role::PARENT],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }

        $this->command->info('✅ Rôles créés avec succès !');
    }
}
