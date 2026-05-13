<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ParkingTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'tariff_profile_id',
        'ticket_code',
        'barcode',
        'plate',
        'vehicle_type',
        'status',
        'location_number',
        'uses_locker',
        'locker_number',
        'locker_fee',
        'customer_name',
        'customer_phone',
        'created_by',
        'closed_by',
        'entry_time',
        'exit_time',
        'notes',
        'is_lost_ticket',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'is_lost_ticket' => 'boolean',
        'uses_locker' => 'boolean',
        'locker_fee' => 'integer',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function tariffProfile(): BelongsTo
    {
        return $this->belongsTo(TariffProfile::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function portalSyncJob(): HasOne
    {
        return $this->hasOne(PortalSyncJob::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
