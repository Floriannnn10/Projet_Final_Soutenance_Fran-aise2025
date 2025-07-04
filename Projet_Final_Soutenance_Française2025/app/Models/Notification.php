<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'type',
        'lue_le'
    ];

    protected $casts = [
        'lue_le' => 'datetime',
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
     * Relation avec l'utilisateur
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Vérifier si la notification est lue
     */
    public function isLue(): bool
    {
        return $this->lue_le !== null;
    }

    /**
     * Marquer la notification comme lue
     */
    public function marquerCommeLue(): void
    {
        $this->update(['lue_le' => now()]);
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
     * Scope pour les notifications non lues
     */
    public function scopeNonLues($query)
    {
        return $query->whereNull('lue_le');
    }

    /**
     * Scope pour les notifications lues
     */
    public function scopeLues($query)
    {
        return $query->whereNotNull('lue_le');
    }

    /**
     * Scope pour les notifications récentes (7 derniers jours)
     */
    public function scopeRecentes($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }
}
