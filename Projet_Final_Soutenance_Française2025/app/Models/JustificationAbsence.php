<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JustificationAbsence extends Model
{
    protected $table = 'justifications_absence';
    protected $fillable = [
        'presence_id',
        'justifie_par_user_id',
        'date_justification',
        'motif'
    ];

    protected $casts = [
        'date_justification' => 'date',
    ];

    /**
     * Relation avec la présence
     */
    public function presence(): BelongsTo
    {
        return $this->belongsTo(Presence::class);
    }

    /**
     * Relation avec l'utilisateur qui a justifié l'absence
     */
    public function justifiePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'justifie_par_user_id');
    }

    /**
     * Obtenir l'étudiant concerné par cette justification
     */
    public function getEtudiantAttribute()
    {
        return $this->presence->etudiant;
    }

    /**
     * Obtenir le cours concerné par cette justification
     */
    public function getCoursAttribute()
    {
        return $this->presence->planning;
    }

    /**
     * Obtenir le nom complet de l'utilisateur qui a justifié
     */
    public function getJustifieParNomAttribute(): string
    {
        return $this->justifiePar ? $this->justifiePar->nom_complet : 'Inconnu';
    }

    /**
     * Vérifier si la justification est récente (moins de 30 jours)
     */
    public function isRecente(): bool
    {
        // diffInDays est une méthode de la classe Carbon qui permet de calculer la différence en jours entre deux dates
        return $this->date_justification->diffInDays(now()) <= 30;
    }

    /**
     * Obtenir la date de justification formatée
     */
    public function getDateJustificationFormateeAttribute(): string
    {
        return $this->date_justification->format('d/m/Y');
    }
}
