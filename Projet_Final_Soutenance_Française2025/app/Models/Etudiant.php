<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Etudiant extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

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
     * Relation avec l'utilisateur (user)
     */
    public function user()
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
     * Relation avec les justifications d'absence via les présences
     */
    public function justifications()
    {
        return $this->hasManyThrough(JustificationAbsence::class, Presence::class);
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
            ->whereHas('courseSession', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('start_time', [$dateDebut, $dateFin]);
                }
                $q->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('name', '!=', 'annulee');
                });
            });

        $totalCours = $baseQuery->count();

        if ($totalCours === 0) {
            return 0;
        }

        // Exclure les absences justifiées du calcul
        $absencesJustifiees = $this->presences()
            ->whereHas('courseSession', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('start_time', [$dateDebut, $dateFin]);
                }
                $q->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('name', '!=', 'annulee');
                });
            })
            ->whereHas('status', function ($q) {
                $q->where('name', 'absent');
            })
            ->where('is_justifie', true)
            ->count();

        // Cours effectifs à considérer (excluant les absences justifiées)
        $coursEffectifs = $totalCours - $absencesJustifiees;

        // Si aucun cours effectif, l'étudiant n'a pas pu être évalué
        if ($coursEffectifs === 0) {
            return 0; // 0% car il n'a pas pu être évalué
        }

        $presences = $this->presences()
            ->whereHas('courseSession', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('start_time', [$dateDebut, $dateFin]);
                }
                $q->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('name', '!=', 'annulee');
                });
            })
            ->whereHas('status', function ($q) {
                $q->where('name', 'present');
            })
            ->count();

        $retards = $this->presences()
            ->whereHas('courseSession', function ($q) use ($dateDebut, $dateFin) {
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('start_time', [$dateDebut, $dateFin]);
                }
                $q->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('name', '!=', 'annulee');
                });
            })
            ->whereHas('status', function ($q) {
                $q->where('name', 'retard');
            })
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
            ->whereHas('courseSession', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('start_time', [$dateDebut, $dateFin]);
                }
                $q->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('name', '!=', 'annulee');
                });
            });

        $totalCours = $baseQuery->count();

        if ($totalCours === 0) {
            return 0;
        }

        // Exclure les absences justifiées du calcul
        $absencesJustifiees = $this->presences()
            ->whereHas('courseSession', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('start_time', [$dateDebut, $dateFin]);
                }
                $q->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('name', '!=', 'annulee');
                });
            })
            ->whereHas('status', function ($q) {
                $q->where('name', 'absent');
            })
            ->where('is_justifie', true)
            ->count();

        // Cours effectifs à considérer (excluant les absences justifiées)
        $coursEffectifs = $totalCours - $absencesJustifiees;

        // Si aucun cours effectif, l'étudiant n'a pas pu être évalué
        if ($coursEffectifs === 0) {
            return 0; // 0% car il n'a pas pu être évalué
        }

        $presences = $this->presences()
            ->whereHas('courseSession', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('start_time', [$dateDebut, $dateFin]);
                }
                $q->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('name', '!=', 'annulee');
                });
            })
            ->whereHas('status', function ($q) {
                $q->where('name', 'present');
            })
            ->count();

        $retards = $this->presences()
            ->whereHas('courseSession', function ($q) use ($matiereId, $dateDebut, $dateFin) {
                $q->where('matiere_id', $matiereId);
                if ($dateDebut && $dateFin) {
                    $q->whereBetween('start_time', [$dateDebut, $dateFin]);
                }
                $q->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('name', '!=', 'annulee');
                });
            })
            ->whereHas('status', function ($q) {
                $q->where('name', 'retard');
            })
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
     * Un étudiant droppé dans une matière devra reprendre ce module l'année prochaine
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
     * - ET qu'il n'est pas droppé (taux > 30%)
     * L'étudiant peut passer en classe supérieure mais aura besoin d'un suivi renforcé
     */
    public function necessiteAssistance($matiereId, $dateDebut = null, $dateFin = null): bool
    {
        $tauxPresence = $this->calculerTauxPresenceMatiere($matiereId, $dateDebut, $dateFin);
        // L'étudiant nécessite une assistance si son taux est entre 30% et 40% ET qu'il n'est pas droppé
        return $tauxPresence > 30 && $tauxPresence <= 40;
    }

    /**
     * Vérifier si l'étudiant est "droppé" globalement
     * Un étudiant est considéré comme "droppé" si :
     * - Son taux de présence global est <= 30% (très faible assiduité)
     * - OU s'il a 0% de présence (même avec absences justifiées) car il ne peut pas être évalué
     * Un étudiant droppé globalement devra reprendre les modules concernés l'année prochaine
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
     * - ET qu'il n'est pas droppé globalement (taux > 30%)
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
            ->whereHas('status', function ($q) {
                $q->where('name', 'absent');
            })
            ->where('is_justifie', false)
            ->with('courseSession.matiere', 'courseSession.typeCours')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir les absences justifiées
     */
    public function getAbsencesJustifiees()
    {
        return $this->presences()
            ->whereHas('status', function ($q) {
                $q->where('name', 'absent');
            })
            ->where('is_justifie', true)
            ->with('courseSession.matiere', 'courseSession.typeCours')
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

        if ($matiereId) {
            // Notification pour une matière spécifique
            $message = "🚨 ALERTE DROPPÉ : L'étudiant {$this->nom_complet} a un taux de présence de {$tauxPresence}% en {$matiereNom}. Il est considéré comme 'droppé' (taux ≤ 30%). L'étudiant n'est plus autorisé à suivre ce module et devra le reprendre l'année prochaine. Action requise immédiate.";
        } else {
            // Notification globale
            $message = "🚨 ALERTE DROPPÉ GLOBAL : L'étudiant {$this->nom_complet} a un taux de présence global de {$tauxPresence}%. Il est considéré comme 'droppé' dans toutes les matières (taux ≤ 30%). L'étudiant devra reprendre les modules concernés l'année prochaine. Action requise immédiate.";
        }

        // Collecter tous les utilisateurs à notifier
        $userIds = [];

        // Notifier les parents
        foreach ($this->parents as $parent) {
            $userIds[] = $parent->id;
        }

        // Notifier les coordinateurs
        $coordinateurs = \App\Models\User::whereHas('role', function($query) {
            $query->where('nom', 'Coordinateur');
        })->get();

        foreach ($coordinateurs as $coordinateur) {
            $userIds[] = $coordinateur->id;
        }

        // Notifier les enseignants de la matière (si spécifiée)
        if ($matiereId) {
            $enseignants = \App\Models\Enseignant::whereHas('plannings', function($query) use ($matiereId) {
                $query->where('matiere_id', $matiereId);
            })->get();

            foreach ($enseignants as $enseignant) {
                $userIds[] = $enseignant->user_id;
            }
        }

        // Créer la notification et l'envoyer à tous les utilisateurs
        if (!empty($userIds)) {
            \App\Models\Notification::creerEtEnvoyer($message, 'droppé', array_unique($userIds));
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

        $message = "📚 ATTENTION : L'étudiant {$this->nom_complet} a un taux de présence faible ({$tauxPresence}%) en {$matiereNom}. Il peut passer en classe supérieure mais nécessite une assistance et un suivi renforcé pour améliorer son assiduité. Action recommandée.";

        // Collecter tous les utilisateurs à notifier
        $userIds = [];

        // Notifier les parents
        foreach ($this->parents as $parent) {
            $userIds[] = $parent->id;
        }

        // Notifier les coordinateurs
        $coordinateurs = \App\Models\User::whereHas('role', function($query) {
            $query->where('nom', 'Coordinateur');
        })->get();

        foreach ($coordinateurs as $coordinateur) {
            $userIds[] = $coordinateur->id;
        }

        // Notifier les enseignants de la matière (si spécifiée)
        if ($matiereId) {
            $enseignants = \App\Models\Enseignant::whereHas('plannings', function($query) use ($matiereId) {
                $query->where('matiere_id', $matiereId);
            })->get();

            foreach ($enseignants as $enseignant) {
                $userIds[] = $enseignant->user_id;
            }
        }

        // Créer la notification et l'envoyer à tous les utilisateurs
        if (!empty($userIds)) {
            \App\Models\Notification::creerEtEnvoyer($message, 'assistance', array_unique($userIds));
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
