<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ClasseSeeder::class,
            MatiereSeeder::class,
            TypeCoursSeeder::class,
            UserSeeder::class,
            PlanningSeeder::class,
            ParentEtudiantSeeder::class,
            PresenceSeeder::class,
            JustificationAbsenceSeeder::class,
            NotificationSeeder::class,
        ]);

        $this->command->info('🎉 Base de données IFRAN peuplée avec succès !');
        $this->command->info('📊 Données créées :');
        $this->command->info('   - 5 rôles');
        $this->command->info('   - 7 classes');
        $this->command->info('   - 8 matières');
        $this->command->info('   - 3 types de cours');
        $this->command->info('   - 38 utilisateurs (1 admin, 2 coordinateurs, 5 enseignants, 20 étudiants, 10 parents)');
        $this->command->info('   - Plannings pour 2 semaines');
    }
}
