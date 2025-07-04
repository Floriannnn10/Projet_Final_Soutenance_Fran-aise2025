<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etudiant extends Model
{
    protected $fillable = [
        'user_id',
        'classe_id',
        'prenom',
        'nom',
        'date_naissance',
        'photo'
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec la classe
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec les présences
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Relation avec les parents (many-to-many)
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_etudiant', 'etudiant_id', 'parent_id')
            ->whereHas('role', function ($query) {
                $query->where('nom', Role::PARENT);
            });
    }

    /**
     * Obtenir le nom complet de l'étudiant
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Calculer le taux de présence global pour une période donnée
     */
    public function calculerTauxPresenceGlobal($dateDebut = null, $dateFin = null): float
    {
        $query = $this->presences()
            ->whereHas('planning', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            });

        $totalCours = $query->count();

        if ($totalCours === 0) {
            return 0;
        }

        $presences = $query->where('statut', 'present')->count();
        $retards = $query->where('statut', 'retard')->count();

        // Un retard compte pour 0.5 présence
        $taux = (($presences + ($retards * 0.5)) / $totalCours) * 100;

        return round($taux, 2);
    }

    /**
     * Calculer le taux de présence par matière
     */
    public function calculerTauxPresenceMatiere($matiereId, $dateDebut = null, $dateFin = null): float
    {
        $query = $this->presences()
            ->whereHas('planning', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            });

        $totalCours = $query->count();

        if ($totalCours === 0) {
            return 0;
        }

        $presences = $query->where('statut', 'present')->count();
        $retards = $query->where('statut', 'retard')->count();

        $taux = (($presences + ($retards * 0.5)) / $totalCours) * 100;

        return round($taux, 2);
    }

    /**
     * Calculer la note d'assiduité (0/20 à 20/20)
     */
    public function calculerNoteAssiduite($matiereId = null, $dateDebut = null, $dateFin = null): float
    {
        if ($matiereId) {
            $taux = $this->calculerTauxPresenceMatiere($matiereId, $dateDebut, $dateFin);
        } else {
            $taux = $this->calculerTauxPresenceGlobal($dateDebut, $dateFin);
        }

        // Convertir le taux en note sur 20
        $note = ($taux / 100) * 20;

        return round($note, 2);
    }

    /**
     * Vérifier si l'étudiant est "droppé" dans une matière (taux <= 30%)
     */
    public function estDroppe($matiereId, $dateDebut = null, $dateFin = null): bool
    {
        $taux = $this->calculerTauxPresenceMatiere($matiereId, $dateDebut, $dateFin);
        return $taux <= 30;
    }

    /**
     * Obtenir les absences non justifiées
     */
    public function getAbsencesNonJustifiees()
    {
        return $this->presences()
            ->where('statut', 'absent')
            ->where('is_justifie', false)
            ->with('planning.matiere', 'planning.typeCours')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir les absences justifiées
     */
    public function getAbsencesJustifiees()
    {
        return $this->presences()
            ->where('statut', 'absent')
            ->where('is_justifie', true)
            ->with('planning.matiere', 'planning.typeCours')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
