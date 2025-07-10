<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planning extends Model
{
    protected $fillable = [
        'classe_id',
        'matiere_id',
        'enseignant_id',
        'type_cours_id',
        'date',
        'heure_debut',
        'heure_fin',
        'is_annule',
        'original_planning_id'
    ];

    protected $casts = [
        'date' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'is_annule' => 'boolean',
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
        return $this->belongsTo(TypeCours::class, 'type_cours_id');
    }

    /**
     * Relation avec les présences
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Relation avec le planning original (pour les cours reportés)
     */
    public function planningOriginal(): BelongsTo
    {
        return $this->belongsTo(Planning::class, 'original_planning_id');
    }

    /**
     * Relation avec les plannings reportés
     */
    public function planningsReportes(): HasMany
    {
        return $this->hasMany(Planning::class, 'original_planning_id');
    }

    /**
     * Obtenir la durée du cours en minutes
     */
    public function getDureeMinutesAttribute(): int
    {
        return $this->heure_debut->diffInMinutes($this->heure_fin);
    }

    /**
     * Obtenir la durée du cours formatée
     */
    public function getDureeFormateeAttribute(): string
    {
        $minutes = $this->duree_minutes;
        $heures = floor($minutes / 60);
        $minutesRestantes = $minutes % 60;

        if ($heures > 0) {
            return "{$heures}h" . ($minutesRestantes > 0 ? " {$minutesRestantes}min" : "");
        }

        return "{$minutesRestantes}min";
    }

    /**
     * Vérifier si le cours peut encore être modifié (dans les 2 semaines)
     */
    public function peutEtreModifie(): bool
    {
        return $this->date->addWeeks(2)->isFuture();
    }

    /**
     * Obtenir les présences pour ce cours
     */
    public function getPresences()
    {
        return $this->presences()
            ->with('etudiant')
            ->orderBy('etudiant.nom')
            ->orderBy('etudiant.prenom')
            ->get();
    }

    /**
     * Obtenir le nombre d'étudiants présents
     */
    public function getNombrePresentsAttribute(): int
    {
        return $this->presences()->whereHas('status', function($q) { $q->where('name', 'present'); })->count();
    }

    /**
     * Obtenir le nombre d'étudiants en retard
     */
    public function getNombreRetardsAttribute(): int
    {
        return $this->presences()->whereHas('status', function($q) { $q->where('name', 'retard'); })->count();
    }

    /**
     * Obtenir le nombre d'étudiants absents
     */
    public function getNombreAbsentsAttribute(): int
    {
        return $this->presences()->whereHas('status', function($q) { $q->where('name', 'absent'); })->count();
    }

    /**
     * Obtenir le taux de présence pour ce cours
     */
    public function getTauxPresenceAttribute(): float
    {
        $total = $this->presences()->count();

        if ($total === 0) {
            return 0;
        }

        $presents = $this->nombre_presents;
        $retards = $this->nombre_retards;

        // Un retard compte pour 0.5 présence
        $taux = (($presents + ($retards * 0.5)) / $total) * 100;

        return round($taux, 2);
    }
}
