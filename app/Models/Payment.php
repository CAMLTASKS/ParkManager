<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_ticket_id',
        'user_id',
        'method',
        'subtotal',
        'discount',
        'surcharge',
        'tax',
        'total',
        'received_amount',
        'change_amount',
        'paid_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ParkingTicket::class, 'parking_ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
