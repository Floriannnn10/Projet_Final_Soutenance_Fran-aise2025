<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\ParentUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les rôles
        $adminRole = Role::where('nom', Role::ADMIN)->first();
        $coordinateurRole = Role::where('nom', Role::COORDINATEUR)->first();
        $enseignantRole = Role::where('nom', Role::ENSEIGNANT)->first();
        $etudiantRole = Role::where('nom', Role::ETUDIANT)->first();
        $parentRole = Role::where('nom', Role::PARENT)->first();

        // Créer l'administrateur
        $admin = User::create([
            'nom' => 'Admin',
            'prenom' => 'IFRAN',
            'email' => 'admin@ifran.fr',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Créer les coordinateurs
        $coordinateurs = [
            ['nom' => 'Dupont', 'prenom' => 'Marie', 'email' => 'marie.dupont@ifran.fr'],
            ['nom' => 'Martin', 'prenom' => 'Pierre', 'email' => 'pierre.martin@ifran.fr'],
        ];

        foreach ($coordinateurs as $coord) {
            $user = User::create([
                'nom' => $coord['nom'],
                'prenom' => $coord['prenom'],
                'email' => $coord['email'],
                'password' => Hash::make('password'),
                'role_id' => $coordinateurRole->id,
            ]);

            Coordinateur::create([
                'user_id' => $user->id,
                'prenom' => $coord['prenom'],
                'nom' => $coord['nom'],
            ]);
        }

        // Créer les enseignants
        $enseignants = [
            ['nom' => 'Bernard', 'prenom' => 'Sophie', 'email' => 'sophie.bernard@ifran.fr'],
            ['nom' => 'Leroy', 'prenom' => 'Jean', 'email' => 'jean.leroy@ifran.fr'],
            ['nom' => 'Moreau', 'prenom' => 'Claire', 'email' => 'claire.moreau@ifran.fr'],
            ['nom' => 'Petit', 'prenom' => 'Thomas', 'email' => 'thomas.petit@ifran.fr'],
            ['nom' => 'Robert', 'prenom' => 'Isabelle', 'email' => 'isabelle.robert@ifran.fr'],
        ];

        foreach ($enseignants as $ens) {
            $user = User::create([
                'nom' => $ens['nom'],
                'prenom' => $ens['prenom'],
                'email' => $ens['email'],
                'password' => Hash::make('password'),
                'role_id' => $enseignantRole->id,
            ]);

            Enseignant::create([
                'user_id' => $user->id,
                'prenom' => $ens['prenom'],
                'nom' => $ens['nom'],
            ]);
        }

        // Créer les étudiants
        $classes = Classe::all();
        $etudiants = [
            ['nom' => 'Dubois', 'prenom' => 'Lucas', 'email' => 'lucas.dubois@student.ifran.fr'],
            ['nom' => 'Garcia', 'prenom' => 'Emma', 'email' => 'emma.garcia@student.ifran.fr'],
            ['nom' => 'Roux', 'prenom' => 'Hugo', 'email' => 'hugo.roux@student.ifran.fr'],
            ['nom' => 'Simon', 'prenom' => 'Léa', 'email' => 'lea.simon@student.ifran.fr'],
            ['nom' => 'Michel', 'prenom' => 'Jules', 'email' => 'jules.michel@student.ifran.fr'],
            ['nom' => 'Lefebvre', 'prenom' => 'Chloé', 'email' => 'chloe.lefebvre@student.ifran.fr'],
            ['nom' => 'Girard', 'prenom' => 'Adam', 'email' => 'adam.girard@student.ifran.fr'],
            ['nom' => 'Bonnet', 'prenom' => 'Inès', 'email' => 'ines.bonnet@student.ifran.fr'],
            ['nom' => 'Dupuis', 'prenom' => 'Louis', 'email' => 'louis.dupuis@student.ifran.fr'],
            ['nom' => 'Lambert', 'prenom' => 'Jade', 'email' => 'jade.lambert@student.ifran.fr'],
            ['nom' => 'Fontaine', 'prenom' => 'Paul', 'email' => 'paul.fontaine@student.ifran.fr'],
            ['nom' => 'Rousseau', 'prenom' => 'Alice', 'email' => 'alice.rousseau@student.ifran.fr'],
            ['nom' => 'Blanc', 'prenom' => 'Raphaël', 'email' => 'raphael.blanc@student.ifran.fr'],
            ['nom' => 'Henry', 'prenom' => 'Camille', 'email' => 'camille.henry@student.ifran.fr'],
            ['nom' => 'Garnier', 'prenom' => 'Nathan', 'email' => 'nathan.garnier@student.ifran.fr'],
            ['nom' => 'Faure', 'prenom' => 'Zoé', 'email' => 'zoe.faure@student.ifran.fr'],
            ['nom' => 'Mercier', 'prenom' => 'Ethan', 'email' => 'ethan.mercier@student.ifran.fr'],
            ['nom' => 'Boyer', 'prenom' => 'Nina', 'email' => 'nina.boyer@student.ifran.fr'],
            ['nom' => 'Chevalier', 'prenom' => 'Théo', 'email' => 'theo.chevalier@student.ifran.fr'],
            ['nom' => 'Denis', 'prenom' => 'Mia', 'email' => 'mia.denis@student.ifran.fr'],
        ];

        foreach ($etudiants as $index => $etud) {
            $user = User::create([
                'nom' => $etud['nom'],
                'prenom' => $etud['prenom'],
                'email' => $etud['email'],
                'password' => Hash::make('password'),
                'role_id' => $etudiantRole->id,
            ]);

            // Assigner une classe de manière cyclique
            $classe = $classes[$index % $classes->count()];

            Etudiant::create([
                'user_id' => $user->id,
                'classe_id' => $classe->id,
                'prenom' => $etud['prenom'],
                'nom' => $etud['nom'],
                'date_naissance' => now()->subYears(rand(18, 25)),
            ]);
        }

        // Créer les parents
        $parents = [
            ['nom' => 'Dubois', 'prenom' => 'Marc', 'email' => 'marc.dubois@parent.ifran.fr', 'telephone' => '0123456789'],
            ['nom' => 'Garcia', 'prenom' => 'Ana', 'email' => 'ana.garcia@parent.ifran.fr', 'telephone' => '0123456790'],
            ['nom' => 'Roux', 'prenom' => 'Philippe', 'email' => 'philippe.roux@parent.ifran.fr', 'telephone' => '0123456791'],
            ['nom' => 'Simon', 'prenom' => 'Catherine', 'email' => 'catherine.simon@parent.ifran.fr', 'telephone' => '0123456792'],
            ['nom' => 'Michel', 'prenom' => 'François', 'email' => 'francois.michel@parent.ifran.fr', 'telephone' => '0123456793'],
            ['nom' => 'Lefebvre', 'prenom' => 'Nathalie', 'email' => 'nathalie.lefebvre@parent.ifran.fr', 'telephone' => '0123456794'],
            ['nom' => 'Girard', 'prenom' => 'Laurent', 'email' => 'laurent.girard@parent.ifran.fr', 'telephone' => '0123456795'],
            ['nom' => 'Bonnet', 'prenom' => 'Valérie', 'email' => 'valerie.bonnet@parent.ifran.fr', 'telephone' => '0123456796'],
            ['nom' => 'Dupuis', 'prenom' => 'Stéphane', 'email' => 'stephane.dupuis@parent.ifran.fr', 'telephone' => '0123456797'],
            ['nom' => 'Lambert', 'prenom' => 'Sandrine', 'email' => 'sandrine.lambert@parent.ifran.fr', 'telephone' => '0123456798'],
        ];

        foreach ($parents as $parent) {
            $user = User::create([
                'nom' => $parent['nom'],
                'prenom' => $parent['prenom'],
                'email' => $parent['email'],
                'password' => Hash::make('password'),
                'role_id' => $parentRole->id,
            ]);

            ParentUser::create([
                'user_id' => $user->id,
                'prenom' => $parent['prenom'],
                'nom' => $parent['nom'],
                'telephone' => $parent['telephone'],
            ]);
        }

        $this->command->info('✅ Utilisateurs créés avec succès !');
        $this->command->info('📧 Admin: admin@ifran.fr / password');
        $this->command->info('📧 Coordinateur: marie.dupont@ifran.fr / password');
        $this->command->info('📧 Enseignant: sophie.bernard@ifran.fr / password');
        $this->command->info('📧 Étudiant: lucas.dubois@student.ifran.fr / password');
        $this->command->info('📧 Parent: marc.dubois@parent.ifran.fr / password');
    }
}
