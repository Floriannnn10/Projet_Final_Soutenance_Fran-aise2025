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
        // 1. Créer la nouvelle table notifications (sans user_id)
        Schema::create('notifications_new', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('type'); // Pour catégoriser (ex: 'cours_annule', 'nouvelle_note', 'rappel_paiement')
            $table->timestamps();
        });

        // 2. Créer la table pivot notification_user
        Schema::create('notification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('notification_id')->constrained('notifications_new')->onDelete('cascade');
            $table->timestamp('read_at')->nullable(); // NULL si non lue, timestamp si lue
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'read_at']);
            $table->index(['notification_id']);
        });

        // 3. Migrer les données existantes
        if (Schema::hasTable('notifications')) {
            $notifications = DB::table('notifications')->get();

            foreach ($notifications as $notification) {
                // Insérer dans la nouvelle table
                $newNotificationId = DB::table('notifications_new')->insertGetId([
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'created_at' => $notification->created_at,
                    'updated_at' => $notification->updated_at,
                ]);

                // Créer la relation dans la table pivot
                DB::table('notification_user')->insert([
                    'user_id' => $notification->user_id,
                    'notification_id' => $newNotificationId,
                    'read_at' => $notification->lue_le,
                    'created_at' => $notification->created_at,
                    'updated_at' => $notification->updated_at,
                ]);
            }
        }

        // 4. Supprimer l'ancienne table
        Schema::dropIfExists('notifications');

        // 5. Renommer la nouvelle table
        Schema::rename('notifications_new', 'notifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer l'ancienne structure
        Schema::create('notifications_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('message');
            $table->string('type');
            $table->timestamp('lue_le')->nullable();
            $table->timestamps();
        });

        // Migrer les données vers l'ancienne structure
        $notificationUsers = DB::table('notification_user')->get();

        foreach ($notificationUsers as $notificationUser) {
            $notification = DB::table('notifications')->find($notificationUser->notification_id);

            DB::table('notifications_old')->insert([
                'user_id' => $notificationUser->user_id,
                'message' => $notification->message,
                'type' => $notification->type,
                'lue_le' => $notificationUser->read_at,
                'created_at' => $notificationUser->created_at,
                'updated_at' => $notificationUser->updated_at,
            ]);
        }

        // Supprimer les nouvelles tables
        Schema::dropIfExists('notification_user');
        Schema::dropIfExists('notifications');

        // Renommer l'ancienne table
        Schema::rename('notifications_old', 'notifications');
    }
};
