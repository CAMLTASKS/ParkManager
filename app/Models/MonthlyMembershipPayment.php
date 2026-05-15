<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyMembershipPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_membership_id',
        'user_id',
        'receipt_code',
        'method',
        'amount',
        'period_start',
        'period_end',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(MonthlyMembership::class, 'monthly_membership_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
