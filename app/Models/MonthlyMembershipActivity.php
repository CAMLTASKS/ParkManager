<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyMembershipActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_membership_id',
        'parking_ticket_id',
        'event_type',
        'plate',
        'ticket_code',
        'occurred_at',
        'notes',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(MonthlyMembership::class, 'monthly_membership_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ParkingTicket::class, 'parking_ticket_id');
    }
}
