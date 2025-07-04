<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coordinateur extends Model
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
     * Relation avec les justifications d'absence (qu'il peut créer)
     */
    public function justificationsAbsence(): HasMany
    {
        return $this->hasMany(JustificationAbsence::class, 'justifie_par_user_id', 'user_id');
    }

    /**
     * Obtenir le nom complet du coordinateur
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Obtenir tous les plannings (en tant que coordinateur, il peut voir tous les emplois du temps)
     */
    public function getAllPlannings($dateDebut = null, $dateFin = null)
    {
        $query = Planning::with(['classe', 'matiere', 'enseignant', 'typeCours'])
            ->where('is_annule', false);

        if ($dateDebut && $dateFin) {
            $query->whereBetween('date', [$dateDebut, $dateFin]);
        }

        return $query->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
    }

    /**
     * Obtenir les statistiques globales
     */
    public function getStatistiquesGlobales($dateDebut = null, $dateFin = null)
    {
        $query = Planning::where('is_annule', false);

        if ($dateDebut && $dateFin) {
            $query->whereBetween('date', [$dateDebut, $dateFin]);
        }

        $totalCours = $query->count();
        $coursPresentiel = $query->whereHas('typeCours', function ($q) {
            $q->where('nom', TypeCours::PRESENTIEL);
        })->count();
        $coursElearning = $query->whereHas('typeCours', function ($q) {
            $q->where('nom', TypeCours::ELEARNING);
        })->count();
        $coursWorkshop = $query->whereHas('typeCours', function ($q) {
            $q->where('nom', TypeCours::WORKSHOP);
        })->count();

        return [
            'total_cours' => $totalCours,
            'presentiel' => $coursPresentiel,
            'elearning' => $coursElearning,
            'workshop' => $coursWorkshop,
        ];
    }
}
