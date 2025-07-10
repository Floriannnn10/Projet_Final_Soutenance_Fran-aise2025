<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionStatus extends Model
{
    protected $fillable = [
        'name',
        'display_name'
    ];

    /**
     * Relation avec les sessions de cours
     */
    public function courseSessions(): HasMany
    {
        return $this->hasMany(CourseSession::class, 'status_id');
    }

    /**
     * Scope pour les statuts actifs
     */
    public function scopeActifs($query)
    {
        return $query->whereIn('name', ['prevue', 'terminee']);
    }

    /**
     * Scope pour les statuts inactifs
     */
    public function scopeInactifs($query)
    {
        return $query->whereIn('name', ['annulee', 'reportee']);
    }
}
