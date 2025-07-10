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
        // 1. Table pour les statuts de présence
        Schema::create('presence_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'present', 'retard', 'absent'
            $table->string('display_name'); // 'Présent', 'En retard', 'Absent'
            $table->timestamps();
        });

        // 2. Table pour les statuts de session de cours
        Schema::create('session_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'prevue', 'annulee', 'reportee', 'terminee'
            $table->string('display_name'); // 'Prévue', 'Annulée', 'Reportée', 'Terminée'
            $table->timestamps();
        });

        // 3. Table pour les types de notifications
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'cours_annule', 'nouvelle_note', 'rappel_paiement'
            $table->string('display_name'); // 'Cours annulé', 'Nouvelle note', 'Rappel de paiement'
            $table->timestamps();
        });

        // 4. Peupler les tables avec les données de base
        $this->seedReferenceTables();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_types');
        Schema::dropIfExists('session_statuses');
        Schema::dropIfExists('presence_statuses');
    }

    /**
     * Peupler les tables de référence
     */
    private function seedReferenceTables(): void
    {
        // Statuts de présence
        DB::table('presence_statuses')->insert([
            ['name' => 'present', 'display_name' => 'Présent', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'retard', 'display_name' => 'En retard', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'absent', 'display_name' => 'Absent', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Statuts de session de cours
        DB::table('session_statuses')->insert([
            ['name' => 'prevue', 'display_name' => 'Prévue', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'annulee', 'display_name' => 'Annulée', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'reportee', 'display_name' => 'Reportée', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'terminee', 'display_name' => 'Terminée', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Types de notifications
        DB::table('notification_types')->insert([
            ['name' => 'cours_annule', 'display_name' => 'Cours annulé', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'nouvelle_note', 'display_name' => 'Nouvelle note', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'rappel_paiement', 'display_name' => 'Rappel de paiement', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'seance_reportee', 'display_name' => 'Séance reportée', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'nouveau_cours', 'display_name' => 'Nouveau cours', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
