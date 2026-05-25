<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'travel_document_id',
        'type',
        'title',
        'body',
        'is_broadcast',
        'read_at',
    ];

    protected $casts = [
        'is_broadcast' => 'boolean',
        'read_at'      => 'datetime',
    ];

    // ── Relations ──────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function travelDocument()
    {
        return $this->belongsTo(TravelDocument::class);
    }

    // ── Scopes ─────────────────────────────────────────────
    /** Notifikasi milik driver tertentu (personal + broadcast) */
    public function scopeForDriver(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_broadcast', true);
        });
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}