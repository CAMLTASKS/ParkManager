@php
    $modal = session('modal');
    $validationError = $errors->any() ? $errors->first() : null;
@endphp

@if ($modal || $validationError)
    <div class="modal-backdrop is-visible" data-modal>
        <div class="modal-card {{ $modal['type'] ?? 'danger' }} {{ ($modal['large'] ?? false) ? 'modal-card-lg monthly-overdue-modal' : '' }}">
            <button class="modal-close" type="button" data-modal-close>&times;</button>
            <span class="modal-kicker">{{ strtoupper($modal['type'] ?? 'error') }}</span>
            <h3>{{ $modal['title'] ?? 'No fue posible continuar' }}</h3>
            <p>{{ $modal['message'] ?? $validationError }}</p>
        </div>
    </div>
@endif
