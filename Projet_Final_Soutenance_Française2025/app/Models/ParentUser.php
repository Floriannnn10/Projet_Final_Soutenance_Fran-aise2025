<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentUser extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'prenom',
        'nom',
        'telephone',
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
     * Relation avec les étudiants (ses enfants)
     */
    public function enfants(): BelongsToMany
    {
        return $this->belongsToMany(Etudiant::class, 'parent_etudiant', 'parent_id', 'etudiant_id');
    }

    /**
     * Obtenir le nom complet du parent
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Obtenir les informations de présence d'un enfant spécifique
     */
    public function getInformationsEnfant($etudiantId, $dateDebut = null, $dateFin = null)
    {
        $enfant = $this->enfants()->find($etudiantId);

        if (!$enfant) {
            return null;
        }

        $tauxGlobal = $enfant->calculerTauxPresenceGlobal($dateDebut, $dateFin);
        $noteAssiduite = $enfant->calculerNoteAssiduite(null, $dateDebut, $dateFin);
        $absencesNonJustifiees = $enfant->getAbsencesNonJustifiees();
        $absencesJustifiees = $enfant->getAbsencesJustifiees();

        return [
            'enfant' => $enfant,
            'taux_presence_global' => $tauxGlobal,
            'note_assiduite' => $noteAssiduite,
            'absences_non_justifiees' => $absencesNonJustifiees,
            'absences_justifiees' => $absencesJustifiees,
        ];
    }

    /**
     * Obtenir l'emploi du temps d'un enfant
     */
    public function getEmploiTempsEnfant($etudiantId, $dateDebut = null, $dateFin = null)
    {
        $enfant = $this->enfants()->find($etudiantId);

        if (!$enfant) {
            return collect();
        }

        $query = $enfant->classe->plannings()
            ->where('is_annule', false)
            ->with(['matiere', 'enseignant', 'typeCours']);

        if ($dateDebut && $dateFin) {
            $query->whereBetween('date', [$dateDebut, $dateFin]);
        }

        return $query->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
    }
}
