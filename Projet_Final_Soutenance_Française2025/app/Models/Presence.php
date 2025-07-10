<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    protected $fillable = [
        'etudiant_id',
        'course_session_id',
        'presence_status_id',
        'enregistre_le',
        'enregistre_par_user_id'
    ];

    protected $casts = [
        'enregistre_le' => 'datetime',
    ];

    /**
     * Relation avec l'étudiant
     */
    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    /**
     * Relation avec la session de cours
     */
    public function courseSession(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class);
    }

    /**
     * Relation avec le statut de présence
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(PresenceStatus::class, 'presence_status_id');
    }

    /**
     * Relation avec l'utilisateur qui a enregistré la présence
     */
    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par_user_id');
    }

    /**
     * Vérifier si la présence est enregistrée comme présente
     */
    public function isPresent(): bool
    {
        return $this->status->name === 'present';
    }

    /**
     * Vérifier si la présence est enregistrée comme retard
     */
    public function isRetard(): bool
    {
        return $this->status->name === 'retard';
    }

    /**
     * Vérifier si la présence est enregistrée comme absente
     */
    public function isAbsent(): bool
    {
        return $this->status->name === 'absent';
    }

    /**
     * Obtenir le libellé du statut en français
     */
    public function getStatutLibelleAttribute(): string
    {
        return $this->status->display_name ?? 'Inconnu';
    }

    /**
     * Obtenir la classe CSS pour le statut
     */
    public function getStatutCssAttribute(): string
    {
        return match($this->status->name) {
            'present' => 'text-success',
            'retard' => 'text-warning',
            'absent' => 'text-danger',
            default => 'text-muted'
        };
    }

    /**
     * Vérifier si la présence peut encore être modifiée (dans les 2 semaines)
     */
    public function peutEtreModifiee(): bool
    {
        return $this->courseSession && $this->courseSession->start_time->isAfter(now()->subWeeks(2));
    }

    /**
     * Enregistrer automatiquement la date d'enregistrement
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($presence) {
            if (!$presence->enregistre_le) {
                $presence->enregistre_le = now();
            }
        });
    }

    /**
     * Scope pour les présences positives
     */
    public function scopePresentes($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'present');
        });
    }

    /**
     * Scope pour les absences
     */
    public function scopeAbsences($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'absent');
        });
    }

    /**
     * Scope pour les retards
     */
    public function scopeRetards($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'retard');
        });
    }
}
