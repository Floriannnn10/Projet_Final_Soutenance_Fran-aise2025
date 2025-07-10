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
        // 1. Créer une nouvelle table presences avec la nouvelle structure
        if (!Schema::hasTable('presences_new')) {
            Schema::create('presences_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('etudiant_id')->constrained('etudiants');
                $table->foreignId('course_session_id')->constrained('course_sessions'); // Remplace planning_id
                $table->foreignId('presence_status_id')->constrained('presence_statuses'); // Remplace l'ENUM
                $table->timestamp('enregistre_le')->nullable();
                $table->foreignId('enregistre_par_user_id')->nullable()->constrained('users');
                $table->timestamps();

                // Index pour optimiser les requêtes
                $table->index(['etudiant_id', 'course_session_id']);
                $table->index(['presence_status_id']);
            });
        }

        // 2. Migrer les données existantes seulement si la table presences existe encore
        if (Schema::hasTable('presences') && Schema::hasTable('course_sessions')) {
            $presences = DB::table('presences')->get();

            foreach ($presences as $presence) {
                // Trouver la session de cours correspondante
                $planning = DB::table('plannings')->find($presence->planning_id);
                if ($planning) {
                    $courseSession = DB::table('course_sessions')
                        ->where('classe_id', $planning->classe_id)
                        ->where('matiere_id', $planning->matiere_id)
                        ->where('enseignant_id', $planning->enseignant_id)
                        ->where('start_time', $planning->date . ' ' . $planning->heure_debut)
                        ->first();

                    if ($courseSession) {
                        // Trouver le statut de présence correspondant
                        $statusId = DB::table('presence_statuses')
                            ->where('name', $presence->statut)
                            ->first()->id;

                        // Vérifier si l'enregistrement existe déjà
                        $existing = DB::table('presences_new')
                            ->where('etudiant_id', $presence->etudiant_id)
                            ->where('course_session_id', $courseSession->id)
                            ->first();

                        if (!$existing) {
                            DB::table('presences_new')->insert([
                                'etudiant_id' => $presence->etudiant_id,
                                'course_session_id' => $courseSession->id,
                                'presence_status_id' => $statusId,
                                'enregistre_le' => $presence->enregistre_le,
                                'enregistre_par_user_id' => $presence->enregistre_par_user_id,
                                'created_at' => $presence->created_at,
                                'updated_at' => $presence->updated_at,
                            ]);
                        }
                    }
                }
            }
        }

        // 3. Supprimer toutes les contraintes de clé étrangère qui référencent la table presences
        $this->dropForeignKeysToPresences();

        // 4. Supprimer l'ancienne table si elle existe
        Schema::dropIfExists('presences');

        // 5. Renommer la nouvelle table
        if (Schema::hasTable('presences_new')) {
            Schema::rename('presences_new', 'presences');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer l'ancienne structure
        Schema::create('presences_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants');
            $table->foreignId('planning_id')->constrained('plannings');
            $table->enum('statut', ['present', 'retard', 'absent']);
            $table->timestamp('enregistre_le')->nullable();
            $table->foreignId('enregistre_par_user_id')->nullable()->constrained('users');
            $table->boolean('is_justifie')->default(false);
            $table->timestamps();
        });

        // Migrer les données vers l'ancienne structure
        $presences = DB::table('presences')->get();

        foreach ($presences as $presence) {
            $courseSession = DB::table('course_sessions')->find($presence->course_session_id);
            $status = DB::table('presence_statuses')->find($presence->presence_status_id);

            // Trouver le planning correspondant (approximatif)
            $planning = DB::table('plannings')
                ->where('classe_id', $courseSession->classe_id)
                ->where('matiere_id', $courseSession->matiere_id)
                ->where('enseignant_id', $courseSession->enseignant_id)
                ->first();

            if ($planning) {
                DB::table('presences_old')->insert([
                    'etudiant_id' => $presence->etudiant_id,
                    'planning_id' => $planning->id,
                    'statut' => $status->name,
                    'enregistre_le' => $presence->enregistre_le,
                    'enregistre_par_user_id' => $presence->enregistre_par_user_id,
                    'is_justifie' => false, // Valeur par défaut
                    'created_at' => $presence->created_at,
                    'updated_at' => $presence->updated_at,
                ]);
            }
        }

        // Supprimer la nouvelle table
        Schema::dropIfExists('presences');

        // Renommer l'ancienne table
        Schema::rename('presences_old', 'presences');

        // Recréer les contraintes de clé étrangère
        $this->recreateForeignKeysToPresences();
    }

    /**
     * Supprimer toutes les contraintes de clé étrangère qui référencent la table presences
     */
    private function dropForeignKeysToPresences(): void
    {
        // Supprimer la contrainte dans justifications_absence
        if (Schema::hasTable('justifications_absence')) {
            $columns = Schema::getColumnListing('justifications_absence');
            if (in_array('presence_id', $columns)) {
                Schema::table('justifications_absence', function (Blueprint $table) {
                    $table->dropForeign(['presence_id']);
                    $table->dropColumn('presence_id');
                });
            }
        }

        // Vérifier s'il y a d'autres tables qui référencent presences
        $tables = ['notifications', 'users', 'etudiants', 'classes', 'matieres', 'enseignants'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $columns = Schema::getColumnListing($table);
                if (in_array('presence_id', $columns)) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->dropForeign(['presence_id']);
                        $table->dropColumn('presence_id');
                    });
                }
            }
        }
    }

    /**
     * Recréer les contraintes de clé étrangère vers la table presences
     */
    private function recreateForeignKeysToPresences(): void
    {
        // Recréer la contrainte dans justifications_absence
        if (Schema::hasTable('justifications_absence')) {
            Schema::table('justifications_absence', function (Blueprint $table) {
                $table->foreignId('presence_id')->nullable()->constrained('presences');
            });
        }
    }
};
