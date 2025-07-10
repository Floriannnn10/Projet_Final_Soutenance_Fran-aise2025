<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer l'ancienne table planning car elle est remplacée par course_sessions
        Schema::dropIfExists('plannings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer l'ancienne table planning (structure de base)
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('classes');
            $table->foreignId('matiere_id')->constrained('matieres');
            $table->foreignId('enseignant_id')->constrained('enseignants');
            $table->foreignId('type_cours_id')->constrained('types_cours');
            $table->date('date');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->boolean('is_annule')->default(false);
            $table->unsignedBigInteger('original_planning_id')->nullable();
            $table->foreign('original_planning_id')->references('id')->on('plannings')->nullOnDelete();
            $table->timestamps();
        });
    }
};
