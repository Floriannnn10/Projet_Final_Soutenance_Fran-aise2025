<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    protected $fillable = [
        'message',
        'type'
    ];

    /**
     * Constantes pour les types de notifications
     */
    const TYPE_DROPPE = 'droppé';
    const TYPE_ABSENCE = 'absence';
    const TYPE_COURS_ANNULE = 'cours_annulé';
    const TYPE_COURS_REPORTE = 'cours_reporté';
    const TYPE_SYSTEME = 'système';

    /**
     * Relation many-to-many avec les utilisateurs via la table pivot
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    /**
     * Relation avec la table pivot pour accéder directement aux données de lecture
     */
    public function notificationUsers(): HasMany
    {
        return $this->hasMany(NotificationUser::class);
    }

    /**
     * Vérifier si la notification est lue par un utilisateur spécifique
     */
    public function isLueByUser(int $userId): bool
    {
        return $this->notificationUsers()
                    ->where('user_id', $userId)
                    ->whereNotNull('read_at')
                    ->exists();
    }

    /**
     * Marquer la notification comme lue par un utilisateur
     */
    public function marquerCommeLueParUser(int $userId): void
    {
        $this->notificationUsers()
             ->where('user_id', $userId)
             ->update(['read_at' => now()]);
    }

    /**
     * Obtenir le nombre d'utilisateurs qui ont lu cette notification
     */
    public function getNombreLecteursAttribute(): int
    {
        return $this->notificationUsers()
                    ->whereNotNull('read_at')
                    ->count();
    }

    /**
     * Obtenir le nombre total d'utilisateurs qui ont reçu cette notification
     */
    public function getNombreDestinatairesAttribute(): int
    {
        return $this->notificationUsers()->count();
    }

    /**
     * Vérifier si c'est une notification de type "droppé"
     */
    public function isDroppe(): bool
    {
        return $this->type === self::TYPE_DROPPE;
    }

    /**
     * Vérifier si c'est une notification d'absence
     */
    public function isAbsence(): bool
    {
        return $this->type === self::TYPE_ABSENCE;
    }

    /**
     * Vérifier si c'est une notification de cours annulé
     */
    public function isCoursAnnule(): bool
    {
        return $this->type === self::TYPE_COURS_ANNULE;
    }

    /**
     * Vérifier si c'est une notification de cours reporté
     */
    public function isCoursReporte(): bool
    {
        return $this->type === self::TYPE_COURS_REPORTE;
    }

    /**
     * Obtenir la classe CSS pour le type de notification
     */
    public function getTypeCssAttribute(): string
    {
        return match($this->type) {
            self::TYPE_DROPPE => 'text-danger',
            self::TYPE_ABSENCE => 'text-warning',
            self::TYPE_COURS_ANNULE => 'text-info',
            self::TYPE_COURS_REPORTE => 'text-primary',
            self::TYPE_SYSTEME => 'text-secondary',
            default => 'text-muted'
        };
    }

    /**
     * Obtenir l'icône pour le type de notification
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_DROPPE => 'exclamation-triangle',
            self::TYPE_ABSENCE => 'user-times',
            self::TYPE_COURS_ANNULE => 'times-circle',
            self::TYPE_COURS_REPORTE => 'calendar-plus',
            self::TYPE_SYSTEME => 'info-circle',
            default => 'bell'
        };
    }

    /**
     * Scope pour les notifications récentes (7 derniers jours)
     */
    public function scopeRecentes($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    /**
     * Créer une notification et l'envoyer à plusieurs utilisateurs
     */
    public static function creerEtEnvoyer(string $message, string $type, array $userIds): self
    {
        $notification = self::create([
            'message' => $message,
            'type' => $type
        ]);

        // Créer les relations avec les utilisateurs
        $pivotData = collect($userIds)->map(function ($userId) {
            return [
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        })->toArray();

        $notification->users()->attach($pivotData);

        return $notification;
    }
}
