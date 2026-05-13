<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalSyncJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_ticket_id',
        'ticket_code',
        'event_type',
        'payload',
        'status',
        'attempts',
        'last_error',
        'synced_at',
        'available_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'synced_at' => 'datetime',
        'available_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ParkingTicket::class, 'parking_ticket_id');
    }
}
