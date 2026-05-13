<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TariffProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'vehicle_type',
        'tariff_type',
        'pricing_strategy',
        'billing_mode',
        'charge_unit',
        'charge_interval',
        'unit_rate',
        'threshold_minutes',
        'max_minutes',
        'full_rate',
        'is_full_rate',
        'is_agreement',
        'agreement_hours',
        'daily_cap',
        'grace_entry_minutes',
        'grace_exit_minutes',
        'lost_ticket_fee',
        'tax_percentage',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_full_rate' => 'boolean',
        'is_agreement' => 'boolean',
        'threshold_minutes' => 'integer',
        'max_minutes' => 'integer',
        'full_rate' => 'integer',
    ];
}
