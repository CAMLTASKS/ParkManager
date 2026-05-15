<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class MonthlyMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'tariff_profile_id',
        'customer_name',
        'plate',
        'vehicle_type',
        'vehicle_brand',
        'phone',
        'starts_at',
        'next_payment_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'next_payment_date' => 'date',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function tariffProfile(): BelongsTo
    {
        return $this->belongsTo(TariffProfile::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MonthlyMembershipPayment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(MonthlyMembershipActivity::class);
    }

    public function currentStatus(?Carbon $today = null): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        $today ??= today();

        return $this->next_payment_date && $this->next_payment_date->lt($today) ? 'overdue' : 'active';
    }

    public function daysOverdue(?Carbon $today = null): int
    {
        $today ??= today();

        if (! $this->next_payment_date || ! $this->next_payment_date->lt($today)) {
            return 0;
        }

        return $this->next_payment_date->diffInDays($today);
    }

    public function isActiveCurrent(): bool
    {
        return $this->currentStatus() === 'active';
    }
}
