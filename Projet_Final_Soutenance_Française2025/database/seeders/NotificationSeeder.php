<?php

namespace Database\Seeders;

use App\Models\Etudiant;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $etudiants = Etudiant::all();
        $users = User::all();

        // Création des notifications
        $notifications = [
            [
                'message' => 'Absence non justifiée au cours de Programmation Web du 15/01/2025',
                'type' => 'absence'
            ],
            [
                'message' => 'Attention : vous avez cumulé 3 retards cette semaine. Merci d\'être ponctuel(le).',
                'type' => 'retard'
            ],
            [
                'message' => 'Le cours de Design UI/UX prévu demain à 14h est annulé. Report à la semaine prochaine.',
                'type' => 'annulation'
            ],
            [
                'message' => 'Votre justification d\'absence du 10/01/2025 a été validée par l\'administration.',
                'type' => 'justification'
            ],
            [
                'message' => 'N\'oubliez pas de rendre votre projet de fin de module avant vendredi.',
                'type' => 'rappel'
            ],
        ];

        // Créer des notifications pour les étudiants
        foreach ($etudiants as $etudiant) {
            $nombreNotifications = rand(1, 4);

            for ($i = 0; $i < $nombreNotifications; $i++) {
                $notification = $notifications[array_rand($notifications)];
                $dateCreation = Carbon::now()->subDays(rand(0, 7));

                Notification::create([
                    'user_id' => $etudiant->user_id,
                    'message' => $notification['message'],
                    'type' => $notification['type'],
                    'lue_le' => rand(1, 100) <= 70 ? $dateCreation->copy()->addHours(rand(1, 24)) : null, // 70% lues
                ]);
            }
        }

        // Créer quelques notifications pour les enseignants et coordinateurs
        $staffUsers = $users->whereIn('role_id', [2, 3]); // Coordinateurs et enseignants

        foreach ($staffUsers as $user) {
            $dateCreation = Carbon::now()->subDays(rand(0, 3));

            Notification::create([
                'user_id' => $user->id,
                'message' => 'Un étudiant a soumis une nouvelle justification d\'absence.',
                'type' => 'justification',
                'lue_le' => rand(1, 100) <= 50 ? $dateCreation->copy()->addHours(rand(1, 12)) : null,
            ]);
        }

        $this->command->info('✅ Notifications créées avec succès !');
    }
}
