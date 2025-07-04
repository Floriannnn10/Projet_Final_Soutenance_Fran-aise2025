<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matiere extends Model
{
    protected $fillable = ['nom'];

    /**
     * Relation avec les plannings
     */
    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    /**
     * Obtenir le nombre de cours dispensés pour cette matière
     */
    public function getNombreCoursAttribute(): int
    {
        return $this->plannings()
            ->where('is_annule', false)
            ->count();
    }

    /**
     * Obtenir les cours de cette matière pour une période donnée
     */
    public function getCoursPeriode($dateDebut, $dateFin)
    {
        return $this->plannings()
            ->whereBetween('date', [$dateDebut, $dateFin])
            ->where('is_annule', false)
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
    }
}
