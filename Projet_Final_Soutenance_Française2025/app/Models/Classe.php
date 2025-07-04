<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    protected $fillable = ['nom'];

    /**
     * Relation avec les étudiants
     */
    public function etudiants(): HasMany
    {
        return $this->hasMany(Etudiant::class);
    }

    /**
     * Relation avec les plannings
     */
    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    /**
     * Obtenir le nombre d'étudiants dans la classe
     */
    public function getNombreEtudiantsAttribute(): int
    {
        return $this->etudiants()->count();
    }

    /**
     * Obtenir les plannings de la semaine pour cette classe
     */
    public function getPlanningsSemaine($dateDebut = null)
    {
        if (!$dateDebut) {
            $dateDebut = now()->startOfWeek();
        }

        $dateFin = $dateDebut->copy()->endOfWeek();

        return $this->plannings()
            ->whereBetween('date', [$dateDebut, $dateFin])
            ->where('is_annule', false)
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
    }
}
