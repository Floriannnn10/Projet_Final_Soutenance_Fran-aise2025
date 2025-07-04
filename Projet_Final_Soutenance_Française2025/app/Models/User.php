<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'photo',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation avec le rôle
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relation avec l'étudiant (si l'utilisateur est un étudiant)
     */
    public function etudiant(): HasOne
    {
        return $this->hasOne(Etudiant::class);
    }

    /**
     * Relation avec l'enseignant (si l'utilisateur est un enseignant)
     */
    public function enseignant(): HasOne
    {
        return $this->hasOne(Enseignant::class);
    }

    /**
     * Relation avec le coordinateur (si l'utilisateur est un coordinateur)
     */
    public function coordinateur(): HasOne
    {
        return $this->hasOne(Coordinateur::class);
    }

    /**
     * Relation avec les notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->isAdmin();
    }

    /**
     * Vérifier si l'utilisateur est coordinateur
     */
    public function isCoordinateur(): bool
    {
        return $this->role && $this->role->isCoordinateur();
    }

    /**
     * Vérifier si l'utilisateur est enseignant
     */
    public function isEnseignant(): bool
    {
        return $this->role && $this->role->isEnseignant();
    }

    /**
     * Vérifier si l'utilisateur est étudiant
     */
    public function isEtudiant(): bool
    {
        return $this->role && $this->role->isEtudiant();
    }

    /**
     * Vérifier si l'utilisateur est parent
     */
    public function isParent(): bool
    {
        return $this->role && $this->role->isParent();
    }

    /**
     * Obtenir le nom complet de l'utilisateur
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
