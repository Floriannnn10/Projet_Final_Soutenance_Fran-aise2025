<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeCours extends Model
{
    protected $table = 'types_cours';
    protected $fillable = ['nom'];

    /**
     * Relation avec les plannings
     */
    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    /**
     * Constantes pour les types de cours
     */
    const PRESENTIEL = 'Présentiel';
    const ELEARNING = 'E-learning';
    const WORKSHOP = 'Workshop';

    /**
     * Vérifier si c'est un cours présentiel
     */
    public function isPresentiel(): bool
    {
        return $this->nom === self::PRESENTIEL;
    }

    /**
     * Vérifier si c'est un cours e-learning
     */
    public function isElearning(): bool
    {
        return $this->nom === self::ELEARNING;
    }

    /**
     * Vérifier si c'est un workshop
     */
    public function isWorkshop(): bool
    {
        return $this->nom === self::WORKSHOP;
    }
}
