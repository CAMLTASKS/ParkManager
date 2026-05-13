@php
    $syncStatus = $ticket->portalSyncJob?->status ?? 'missing';
    $syncLabel = match ($syncStatus) {
        'synced' => 'Sincronizado',
        'failed' => 'Pendiente sync',
        'pending' => 'Por sincronizar',
        'missing' => 'Sin registro',
        default => 'Por sincronizar',
    };
@endphp

<span class="sync-badge sync-badge-{{ $syncStatus }}" title="{{ $ticket->portalSyncJob?->last_error ?: $syncLabel }}">
    <span class="sync-badge-dot"></span>
    {{ $syncLabel }}
</span>
