<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Définir les commandes du planificateur de l'application.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Détection automatique des étudiants droppés chaque vendredi à 17h15 (fin de semaine)
        $schedule->job(new \App\Jobs\DetecterEtudiantsDroppes())
                ->weeklyOn(5, '17:15') // Vendredi à 17h15
                ->description('Détection automatique des étudiants droppés - Fin de semaine')
                ->withoutOverlapping()
                ->runInBackground();

        // Détection automatique des étudiants droppés à la fin de chaque semestre à 15h00
        // Semestre 1 : Janvier (15 janvier à 15h00)
        // Semestre 2 : Juin (15 juin à 15h00)
        $schedule->job(new \App\Jobs\DetecterEtudiantsDroppes())
                ->cron('0 15 15 1,6 *') // 15h00 le 15 janvier et 15 juin
                ->description('Détection automatique des étudiants droppés - Fin de semestre')
                ->withoutOverlapping()
                ->runInBackground();

        // Alternative : Si tu veux aussi une détection mensuelle pour le suivi
        // $schedule->job(new \App\Jobs\DetecterEtudiantsDroppes())
        //         ->monthlyOn(15, '15:00')
        //         ->description('Détection mensuelle des étudiants droppés')
        //         ->withoutOverlapping()
        //         ->runInBackground();
    }

    /**
     * Enregistrer les commandes de l'application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 