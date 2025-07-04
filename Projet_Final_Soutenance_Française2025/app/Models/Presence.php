<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Presence extends Model
{
    protected $fillable = [
        'etudiant_id',
        'planning_id',
        'statut',
        'enregistre_le',
        'enregistre_par_user_id',
        'is_justifie'
    ];

    protected $casts = [
        'enregistre_le' => 'datetime',
        'is_justifie' => 'boolean',
    ];

    /**
     * Constantes pour les statuts de présence
     */
    const STATUT_PRESENT = 'present';
    const STATUT_RETARD = 'retard';
    const STATUT_ABSENT = 'absent';

    /**
     * Relation avec l'étudiant
     */
    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    /**
     * Relation avec le planning
     */
    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class);
    }

    /**
     * Relation avec l'utilisateur qui a enregistré la présence
     */
    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par_user_id');
    }

    /**
     * Relation avec la justification d'absence
     */
    public function justificationAbsence(): HasOne
    {
        return $this->hasOne(JustificationAbsence::class);
    }

    /**
     * Vérifier si la présence est enregistrée comme présente
     */
    public function isPresent(): bool
    {
        return $this->statut === self::STATUT_PRESENT;
    }

    /**
     * Vérifier si la présence est enregistrée comme retard
     */
    public function isRetard(): bool
    {
        return $this->statut === self::STATUT_RETARD;
    }

    /**
     * Vérifier si la présence est enregistrée comme absente
     */
    public function isAbsent(): bool
    {
        return $this->statut === self::STATUT_ABSENT;
    }

    /**
     * Obtenir le libellé du statut en français
     */
    public function getStatutLibelleAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_PRESENT => 'Présent',
            self::STATUT_RETARD => 'Retard',
            self::STATUT_ABSENT => 'Absent',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir la classe CSS pour le statut
     */
    public function getStatutCssAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_PRESENT => 'text-success',
            self::STATUT_RETARD => 'text-warning',
            self::STATUT_ABSENT => 'text-danger',
            default => 'text-muted'
        };
    }

    /**
     * Vérifier si la présence peut encore être modifiée (dans les 2 semaines)
     */
    public function peutEtreModifiee(): bool
    {
        return $this->planning && $this->planning->peutEtreModifie();
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
}
