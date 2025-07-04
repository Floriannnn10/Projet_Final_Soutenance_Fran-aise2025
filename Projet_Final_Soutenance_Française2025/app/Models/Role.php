<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['nom'];

    /**
     * Relation avec les utilisateurs
     */
    public function utilisateurs(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Constantes pour les rôles
     */
    const ADMIN = 'Admin';
    const COORDINATEUR = 'Coordinateur';
    const ENSEIGNANT = 'Enseignant';
    const ETUDIANT = 'Étudiant';
    const PARENT = 'Parent';

    /**
     * Vérifier si le rôle est admin
     */
    public function isAdmin(): bool
    {
        return $this->nom === self::ADMIN;
    }

    /**
     * Vérifier si le rôle est coordinateur
     */
    public function isCoordinateur(): bool
    {
        return $this->nom === self::COORDINATEUR;
    }

    /**
     * Vérifier si le rôle est enseignant
     */
    public function isEnseignant(): bool
    {
        return $this->nom === self::ENSEIGNANT;
    }

    /**
     * Vérifier si le rôle est étudiant
     */
    public function isEtudiant(): bool
    {
        return $this->nom === self::ETUDIANT;
    }

    /**
     * Vérifier si le rôle est parent
     */
    public function isParent(): bool
    {
        return $this->nom === self::PARENT;
    }
}
