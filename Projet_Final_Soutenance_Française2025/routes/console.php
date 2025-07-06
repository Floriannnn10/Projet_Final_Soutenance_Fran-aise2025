<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\DetecterEtudiantsDroppes;


Artisan::command('inspire', function () { 
    $this->comment(Inspiring::quote()); 
})->purpose('Display an inspiring quote'); 

// Schedule pour la détection automatique des étudiants "droppés"
Schedule::command('ifran:detecter-droppes --job')
    ->dailyAt('08:00')  // Tous les jours à 8h00
    ->description('Détection automatique des étudiants droppés IFRAN')
    ->withoutOverlapping();
