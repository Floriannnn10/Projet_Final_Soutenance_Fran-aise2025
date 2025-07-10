<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Créer la table course_sessions
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('classes');
            $table->foreignId('matiere_id')->constrained('matieres');
            $table->foreignId('enseignant_id')->constrained('enseignants');
            $table->foreignId('type_cours_id')->constrained('types_cours');
            $table->foreignId('status_id')->constrained('session_statuses');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable(); // 'Salle B204', 'En ligne', etc.
            $table->text('notes')->nullable(); // Notes additionnelles
            $table->unsignedBigInteger('replacement_for_session_id')->nullable();
            $table->foreign('replacement_for_session_id')->references('id')->on('course_sessions')->nullOnDelete();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['classe_id', 'start_time']);
            $table->index(['enseignant_id', 'start_time']);
            $table->index(['status_id']);
        });

        // 2. Migrer les données existantes du planning vers course_sessions
        if (Schema::hasTable('plannings')) {
            $plannings = DB::table('plannings')->get();

            foreach ($plannings as $planning) {
                // Déterminer le statut basé sur is_annule
                $statusId = $planning->is_annule ?
                    DB::table('session_statuses')->where('name', 'annulee')->first()->id :
                    DB::table('session_statuses')->where('name', 'prevue')->first()->id;

                // Créer la date/heure de début et fin
                $startTime = $planning->date . ' ' . $planning->heure_debut;
                $endTime = $planning->date . ' ' . $planning->heure_fin;

                DB::table('course_sessions')->insert([
                    'classe_id' => $planning->classe_id,
                    'matiere_id' => $planning->matiere_id,
                    'enseignant_id' => $planning->enseignant_id,
                    'type_cours_id' => $planning->type_cours_id,
                    'status_id' => $statusId,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'location' => 'Salle par défaut', // Valeur par défaut
                    'created_at' => $planning->created_at,
                    'updated_at' => $planning->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sessions');
    }
};
