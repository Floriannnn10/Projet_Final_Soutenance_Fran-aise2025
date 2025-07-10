<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseSession extends Model
{
    protected $fillable = [
        'classe_id',
        'matiere_id',
        'enseignant_id',
        'type_cours_id',
        'status_id',
        'start_time',
        'end_time',
        'location',
        'notes',
        'replacement_for_session_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relation avec la classe
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec la matière
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Relation avec l'enseignant
     */
    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class);
    }

    /**
     * Relation avec le type de cours
     */
    public function typeCours(): BelongsTo
    {
        return $this->belongsTo(TypeCours::class);
    }

    /**
     * Relation avec le statut de session
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(SessionStatus::class, 'status_id');
    }

    /**
     * Relation avec les présences
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Relation avec la session remplacée (si c'est un remplacement)
     */
    public function sessionRemplacee(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'replacement_for_session_id');
    }

    /**
     * Relation avec la session de remplacement (si cette session est remplacée)
     */
    public function sessionDeRemplacement(): HasOne
    {
        return $this->hasOne(CourseSession::class, 'replacement_for_session_id');
    }

    /**
     * Vérifier si la session est annulée
     */
    public function isAnnulee(): bool
    {
        return $this->status->name === 'annulee';
    }

    /**
     * Vérifier si la session est reportée
     */
    public function isReportee(): bool
    {
        return $this->status->name === 'reportee';
    }

    /**
     * Vérifier si la session est terminée
     */
    public function isTerminee(): bool
    {
        return $this->status->name === 'terminee';
    }

    /**
     * Vérifier si la session est prévue
     */
    public function isPrevue(): bool
    {
        return $this->status->name === 'prevue';
    }

    /**
     * Vérifier si c'est un remplacement
     */
    public function isRemplacement(): bool
    {
        return $this->replacement_for_session_id !== null;
    }

    /**
     * Obtenir la durée de la session en minutes
     */
    public function getDureeMinutesAttribute(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Obtenir la durée de la session formatée
     */
    public function getDureeFormateeAttribute(): string
    {
        $minutes = $this->duree_minutes;
        $heures = intval($minutes / 60);
        $minutesRestantes = $minutes % 60;

        if ($heures > 0) {
            return "{$heures}h" . ($minutesRestantes > 0 ? " {$minutesRestantes}min" : "");
        }

        return "{$minutesRestantes}min";
    }

    /**
     * Scope pour les sessions à venir
     */
    public function scopeAVenir($query)
    {
        return $query->where('start_time', '>', now());
    }

    /**
     * Scope pour les sessions passées
     */
    public function scopePassees($query)
    {
        return $query->where('end_time', '<', now());
    }

    /**
     * Scope pour les sessions d'aujourd'hui
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('start_time', today());
    }

    /**
     * Scope pour les sessions d'une classe
     */
    public function scopePourClasse($query, int $classeId)
    {
        return $query->where('classe_id', $classeId);
    }

    /**
     * Scope pour les sessions d'un enseignant
     */
    public function scopePourEnseignant($query, int $enseignantId)
    {
        return $query->where('enseignant_id', $enseignantId);
    }

    /**
     * Annuler la session
     */
    public function annuler(): void
    {
        $statusId = SessionStatus::where('name', 'annulee')->first()->id;
        $this->update(['status_id' => $statusId]);
    }

    /**
     * Reporter la session
     */
    public function reporter(\DateTime $nouvelleDate): void
    {
        $statusId = SessionStatus::where('name', 'reportee')->first()->id;
        $this->update([
            'status_id' => $statusId,
            'start_time' => $nouvelleDate,
            'end_time' => $nouvelleDate->add(new \DateInterval('PT' . $this->duree_minutes . 'M'))
        ]);
    }

    /**
     * Marquer comme terminée
     */
    public function marquerCommeTerminee(): void
    {
        $statusId = SessionStatus::where('name', 'terminee')->first()->id;
        $this->update(['status_id' => $statusId]);
    }
}
