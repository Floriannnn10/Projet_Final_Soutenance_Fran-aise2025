<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enseignant extends Model
{
    protected $fillable = [
        'user_id',
        'prenom',
        'nom',
        'photo'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec les plannings (cours qu'il dispense)
     */
    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    /**
     * Obtenir le nom complet de l'enseignant
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Obtenir l'emploi du temps de la semaine pour cet enseignant
     */
    public function getEmploiTempsSemaine($dateDebut = null)
    {
        if (!$dateDebut) {
            $dateDebut = now()->startOfWeek();
        }

        $dateFin = $dateDebut->copy()->endOfWeek();

        return $this->plannings()
            ->whereBetween('date', [$dateDebut, $dateFin])
            ->where('is_annule', false)
            ->with(['classe', 'matiere', 'typeCours'])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
    }

    /**
     * Obtenir les cours d'aujourd'hui pour cet enseignant
     */
    public function getCoursAujourdhui()
    {
        return $this->plannings()
            ->where('date', now()->toDateString())
            ->where('is_annule', false)
            ->with(['classe', 'matiere', 'typeCours'])
            ->orderBy('heure_debut')
            ->get();
    }

    /**
     * Obtenir le nombre de cours dispensés par cet enseignant
     */
    public function getNombreCoursDispenses($dateDebut = null, $dateFin = null): int
    {
        $query = $this->plannings()->where('is_annule', false);

        if ($dateDebut && $dateFin) {
            $query->whereBetween('date', [$dateDebut, $dateFin]);
        }

        return $query->count();
    }
}
