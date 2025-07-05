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
     * Calculer le taux de présence global pour une période donnée (excluant les absences justifiées)
     */
    public function calculerTauxPresenceGlobal($dateDebut = null, $dateFin = null): float
    {
        $baseQuery = $this->presences()
            ->whereHas('planning', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            });

        $totalCours = $baseQuery->count();

        if ($totalCours === 0) {
            return 0;
        }

        // Exclure les absences justifiées du calcul
        $absencesJustifiees = $this->presences()
            ->whereHas('planning', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            })
            ->where('statut', 'absent')
            ->where('is_justifie', true)
            ->count();

        // Cours effectifs à considérer (excluant les absences justifiées)
        $coursEffectifs = $totalCours - $absencesJustifiees;

        // Si aucun cours effectif, l'étudiant n'a pas pu être évalué
        if ($coursEffectifs === 0) {
            return 0; // 0% car il n'a pas pu être évalué
        }

        $presences = $this->presences()
            ->whereHas('planning', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            })
            ->where('statut', 'present')
            ->count();

        $retards = $this->presences()
            ->whereHas('planning', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            })
            ->where('statut', 'retard')
            ->count();

        // Un retard compte pour 0.5 présence
        $taux = (($presences + ($retards * 0.5)) / $coursEffectifs) * 100;

        return round($taux, 2);
    }

    /**
     * Calculer le taux de présence par matière (excluant les absences justifiées)
     */
    public function calculerTauxPresenceMatiere($matiereId, $dateDebut = null, $dateFin = null): float
    {
        $baseQuery = $this->presences()
            ->whereHas('planning', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            });

        $totalCours = $baseQuery->count();

        if ($totalCours === 0) {
            return 0;
        }

        // Exclure les absences justifiées du calcul
        $absencesJustifiees = $this->presences()
            ->whereHas('planning', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            })
            ->where('statut', 'absent')
            ->where('is_justifie', true)
            ->count();

        // Cours effectifs à considérer (excluant les absences justifiées)
        $coursEffectifs = $totalCours - $absencesJustifiees;

        // Si aucun cours effectif, l'étudiant n'a pas pu être évalué
        if ($coursEffectifs === 0) {
            return 0; // 0% car il n'a pas pu être évalué
        }

        $presences = $this->presences()
            ->whereHas('planning', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            })
            ->where('statut', 'present')
            ->count();

        $retards = $this->presences()
            ->whereHas('planning', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('date', [$dateDebut, $dateFin]);
                }
                $q->where('is_annule', false);
            })
            ->where('statut', 'retard')
            ->count();

        $taux = (($presences + ($retards * 0.5)) / $coursEffectifs) * 100;

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
     * Vérifier si l'étudiant est "droppé" dans une matière
     * Un étudiant est considéré comme "droppé" si :
     * - Son taux de présence est <= 30% (très faible assiduité)
     * - OU s'il a 0% de présence (même avec absences justifiées) car il ne peut pas être évalué
     * Un étudiant droppé devra reprendre son année
     */
    public function estDroppe($matiereId, $dateDebut = null, $dateFin = null): bool
    {
        $tauxPresence = $this->calculerTauxPresenceMatiere($matiereId, $dateDebut, $dateFin);
        // L'étudiant est "droppé" si son taux de présence est <= 30% ou s'il a 0% (pas évaluable)
        return $tauxPresence <= 30;
    }

    /**
     * Vérifier si l'étudiant nécessite une assistance dans une matière
     * Un étudiant nécessite une assistance si :
     * - Son taux de présence est entre 30% et 40% (assiduité faible mais passable)
     * L'étudiant peut passer en classe supérieure mais aura besoin d'un suivi renforcé
     */
    public function necessiteAssistance($matiereId, $dateDebut = null, $dateFin = null): bool
    {
        $tauxPresence = $this->calculerTauxPresenceMatiere($matiereId, $dateDebut, $dateFin);
        // L'étudiant nécessite une assistance si son taux est entre 30% et 40%
        return $tauxPresence > 30 && $tauxPresence <= 40;
    }

    /**
     * Vérifier si l'étudiant est "droppé" globalement
     * Un étudiant est considéré comme "droppé" si :
     * - Son taux de présence global est <= 30% (très faible assiduité)
     * - OU s'il a 0% de présence (même avec absences justifiées) car il ne peut pas être évalué
     * Un étudiant droppé devra reprendre son année
     */
    public function estDroppeGlobal($dateDebut = null, $dateFin = null): bool
    {
        $tauxPresence = $this->calculerTauxPresenceGlobal($dateDebut, $dateFin);
        return $tauxPresence <= 30;
    }

    /**
     * Vérifier si l'étudiant nécessite une assistance globale
     * Un étudiant nécessite une assistance globale si :
     * - Son taux de présence global est entre 30% et 40%
     * L'étudiant peut passer en classe supérieure mais aura besoin d'un suivi renforcé
     */
    public function necessiteAssistanceGlobal($dateDebut = null, $dateFin = null): bool
    {
        $tauxPresence = $this->calculerTauxPresenceGlobal($dateDebut, $dateFin);
        return $tauxPresence > 30 && $tauxPresence <= 40;
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

    /**
     * Envoyer notification de "droppé" aux parents, coordinateurs et enseignants
     */
    public function envoyerNotificationDroppe($matiereId = null, $dateDebut = null, $dateFin = null): void
    {
        $matiere = $matiereId ? \App\Models\Matiere::find($matiereId) : null;
        $matiereNom = $matiere ? $matiere->nom : 'toutes les matières';

        $tauxPresence = $matiereId ?
            $this->calculerTauxPresenceMatiere($matiereId, $dateDebut, $dateFin) :
            $this->calculerTauxPresenceGlobal($dateDebut, $dateFin);

        $message = "⚠️ ALERTE : L'étudiant {$this->nom_complet} a un taux de présence très faible ({$tauxPresence}%) en {$matiereNom}. Il est considéré comme 'droppé' (taux ≤ 30% ou 0% même avec justifications). L'étudiant devra reprendre son année. Action requise.";

        // Notifier les parents
        foreach ($this->parents as $parent) {
            \App\Models\Notification::create([
                'user_id' => $parent->id,
                'message' => $message,
                'type' => 'droppé',
            ]);
        }

        // Notifier les coordinateurs
        $coordinateurs = \App\Models\User::whereHas('role', function($query) {
            $query->where('nom', 'Coordinateur');
        })->get();

        foreach ($coordinateurs as $coordinateur) {
            \App\Models\Notification::create([
                'user_id' => $coordinateur->id,
                'message' => $message,
                'type' => 'droppé',
            ]);
        }

        // Notifier les enseignants de la matière (si spécifiée)
        if ($matiereId) {
            $enseignants = \App\Models\Enseignant::whereHas('plannings', function($query) use ($matiereId) {
                $query->where('matiere_id', $matiereId);
            })->get();

            foreach ($enseignants as $enseignant) {
                \App\Models\Notification::create([
                    'user_id' => $enseignant->user_id,
                    'message' => $message,
                    'type' => 'droppé',
                ]);
            }
        }
    }

    /**
     * Vérifier et notifier automatiquement si l'étudiant est "droppé"
     */
    public function verifierEtNotifierDroppe($matiereId = null, $dateDebut = null, $dateFin = null): bool
    {
        $estDroppe = $matiereId ? $this->estDroppe($matiereId, $dateDebut, $dateFin) : $this->estDroppeGlobal($dateDebut, $dateFin);

        if ($estDroppe) {
            $this->envoyerNotificationDroppe($matiereId, $dateDebut, $dateFin);
        }

        return $estDroppe;
    }

    /**
     * Envoyer notification d'assistance aux parents, coordinateurs et enseignants
     */
    public function envoyerNotificationAssistance($matiereId = null, $dateDebut = null, $dateFin = null): void
    {
        $matiere = $matiereId ? \App\Models\Matiere::find($matiereId) : null;
        $matiereNom = $matiere ? $matiere->nom : 'toutes les matières';

        $tauxPresence = $matiereId ?
            $this->calculerTauxPresenceMatiere($matiereId, $dateDebut, $dateFin) :
            $this->calculerTauxPresenceGlobal($dateDebut, $dateFin);

        $message = "📚 ATTENTION : L'étudiant {$this->nom_complet} a un taux de présence faible ({$tauxPresence}%) en {$matiereNom}. Il peut passer en classe supérieure mais nécessite une assistance et un suivi renforcé. Action recommandée.";

        // Notifier les parents
        foreach ($this->parents as $parent) {
            \App\Models\Notification::create([
                'user_id' => $parent->id,
                'message' => $message,
                'type' => 'assistance',
            ]);
        }

        // Notifier les coordinateurs
        $coordinateurs = \App\Models\User::whereHas('role', function($query) {
            $query->where('nom', 'Coordinateur');
        })->get();

        foreach ($coordinateurs as $coordinateur) {
            \App\Models\Notification::create([
                'user_id' => $coordinateur->id,
                'message' => $message,
                'type' => 'assistance',
            ]);
        }

        // Notifier les enseignants de la matière (si spécifiée)
        if ($matiereId) {
            $enseignants = \App\Models\Enseignant::whereHas('plannings', function($query) use ($matiereId) {
                $query->where('matiere_id', $matiereId);
            })->get();

            foreach ($enseignants as $enseignant) {
                \App\Models\Notification::create([
                    'user_id' => $enseignant->user_id,
                    'message' => $message,
                    'type' => 'assistance',
                ]);
            }
        }
    }

    /**
     * Vérifier et notifier automatiquement si l'étudiant nécessite une assistance
     */
    public function verifierEtNotifierAssistance($matiereId = null, $dateDebut = null, $dateFin = null): bool
    {
        $necessiteAssistance = $matiereId ? $this->necessiteAssistance($matiereId, $dateDebut, $dateFin) : $this->necessiteAssistanceGlobal($dateDebut, $dateFin);

        if ($necessiteAssistance) {
            $this->envoyerNotificationAssistance($matiereId, $dateDebut, $dateFin);
        }

        return $necessiteAssistance;
    }
}
