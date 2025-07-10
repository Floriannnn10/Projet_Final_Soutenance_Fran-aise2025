<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationUser extends Model
{
    protected $table = 'notification_user';

    protected $fillable = [
        'user_id',
        'notification_id',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la notification
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Vérifier si la notification est lue
     */
    public function isLue(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Marquer comme lue
     */
    public function marquerCommeLue(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Marquer comme non lue
     */
    public function marquerCommeNonLue(): void
    {
        $this->update(['read_at' => null]);
    }
}
