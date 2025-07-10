<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PresenceStatus extends Model
{
    protected $fillable = [
        'name',
        'display_name'
    ];

    /**
     * Relation avec les présences
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class, 'presence_status_id');
    }

    /**
     * Scope pour les statuts positifs (présent)
     */
    public function scopePositifs($query)
    {
        return $query->where('name', 'present');
    }

    /**
     * Scope pour les statuts négatifs (absent, retard)
     */
    public function scopeNegatifs($query)
    {
        return $query->whereIn('name', ['absent', 'retard']);
    }
}
