const body = document.body;
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const loadingOverlay = document.getElementById('loadingOverlay');

if (sidebar && localStorage.getItem('sidebar-collapsed') === 'true') {
    body.classList.add('sidebar-collapsed');
}

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
    });
}

document.querySelectorAll('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = button.closest('[data-modal]');
        if (modal) {
            modal.remove();
        }
    });
});

document.querySelectorAll('[data-open-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        const targetId = button.getAttribute('data-open-modal');
        const modal = document.querySelector(`[data-app-modal-id="${targetId}"]`);
        if (modal) {
            modal.classList.add('is-visible');
        }
    });
});

document.querySelectorAll('[data-close-app-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = button.closest('[data-app-modal-id]');
        if (modal) {
            modal.classList.remove('is-visible');
        }
    });
});

document.querySelectorAll('[data-loading-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        if (loadingOverlay) {
            loadingOverlay.classList.add('is-visible');
        }
    });
});

document.querySelectorAll('.choice-tile input[type="radio"]').forEach((input) => {
    input.addEventListener('change', () => {
        document.querySelectorAll('.choice-tile').forEach((tile) => tile.classList.remove('active'));
        input.closest('.choice-tile')?.classList.add('active');
    });
});

document.querySelectorAll('.visual-choice-card input[type="radio"]').forEach((input) => {
    input.addEventListener('change', () => {
        document.querySelectorAll('.visual-choice-card').forEach((card) => card.classList.remove('active'));
        input.closest('.visual-choice-card')?.classList.add('active');
    });
});

const updateTariffPicker = (picker) => {
    const select = picker.querySelector('[data-tariff-select]');
    const selectedVehicleType = document.querySelector('input[name="vehicle_type"]:checked')?.value;
    const cards = Array.from(picker.querySelectorAll('[data-tariff-card]'));

    if (!select || cards.length === 0) {
        return;
    }

    const hasVehicleMatches = cards.some((card) => !selectedVehicleType || card.dataset.vehicleType === selectedVehicleType);
    const visibleCards = cards.filter((card) => {
        const matches = !selectedVehicleType || !hasVehicleMatches || card.dataset.vehicleType === selectedVehicleType;
        card.hidden = !matches;
        card.classList.toggle('is-hidden', !matches);
        return matches;
    });

    const selectedCard = cards.find((card) => card.querySelector('input')?.value === select.value);
    const selectedCardIsVisible = selectedCard && !selectedCard.hidden;

    if (!selectedCardIsVisible && visibleCards.length > 0) {
        const firstInput = visibleCards[0].querySelector('input');
        if (firstInput) {
            firstInput.checked = true;
            select.value = firstInput.value;
        }
    }

    cards.forEach((card) => {
        const input = card.querySelector('input');
        const isActive = input?.value === select.value;
        if (input) {
            input.checked = isActive;
        }
        card.classList.toggle('active', isActive);
    });
};

document.querySelectorAll('[data-tariff-picker]').forEach((picker) => {
    const select = picker.querySelector('[data-tariff-select]');

    picker.querySelectorAll('[data-tariff-card] input[type="radio"]').forEach((input) => {
        input.addEventListener('change', () => {
            if (select) {
                select.value = input.value;
            }
            updateTariffPicker(picker);
        });
    });

    select?.addEventListener('change', () => updateTariffPicker(picker));
    updateTariffPicker(picker);
});

document.querySelectorAll('input[name="vehicle_type"]').forEach((input) => {
    input.addEventListener('change', () => {
        document.querySelectorAll('[data-tariff-picker]').forEach(updateTariffPicker);
    });
});

document.querySelectorAll('[data-entry-prefill-route]').forEach((input) => {
    input.addEventListener('input', () => {
        const start = input.selectionStart;
        const end = input.selectionEnd;
        input.value = input.value.toUpperCase();
        if (start !== null && end !== null) {
            input.setSelectionRange(start, end);
        }
    });

    input.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
            return;
        }

        const value = input.value.trim().toUpperCase();
        const route = input.getAttribute('data-entry-prefill-route');
        if (!value || !route) {
            return;
        }

        event.preventDefault();
        window.location.href = `${route}?plate_lookup=${encodeURIComponent(value)}`;
    });
});

document.querySelectorAll('[data-locker-toggle]').forEach((toggle) => {
    const form = toggle.closest('form');
    const field = form?.querySelector('[data-locker-number-field]');
    const input = field?.querySelector('input');
    const updateLockerField = () => {
        const enabled = toggle.checked;
        if (field) {
            field.classList.toggle('is-muted', !enabled);
        }
        if (input) {
            input.required = enabled;
            input.disabled = !enabled;
            if (!enabled) {
                input.value = '';
            }
        }
    };

    toggle.addEventListener('change', updateLockerField);
    updateLockerField();
});

const portalSyncUrl = document.querySelector('meta[name="portal-sync-url"]')?.getAttribute('content');
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const portalSyncInterval = Math.max(parseInt(document.querySelector('meta[name="portal-sync-interval"]')?.getAttribute('content') || '5', 10), 1);

const updatePortalSyncWidgets = (payload) => {
    if (!payload || !payload.ok) {
        return;
    }

    document.querySelectorAll('[data-portal-sync-pending]').forEach((node) => {
        node.textContent = payload.pending ?? node.textContent;
    });
    document.querySelectorAll('[data-portal-sync-failed]').forEach((node) => {
        node.textContent = payload.failed_total ?? payload.failed ?? node.textContent;
    });

    const nextRun = new Date(Date.now() + (portalSyncInterval * 60000));
    document.querySelectorAll('[data-portal-sync-next]').forEach((node) => {
        node.textContent = nextRun.toLocaleString('es-CO', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    });

    if ((payload.sent || 0) > 0) {
        const now = new Date();
        document.querySelectorAll('[data-portal-sync-last]').forEach((node) => {
            node.textContent = now.toLocaleString('es-CO', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        });
    }
};

const runPortalSync = (force = false) => {
    if (!portalSyncUrl || !csrfToken) {
        return;
    }

    return fetch(force ? `${portalSyncUrl}?force=1` : portalSyncUrl, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'same-origin',
    }).then((response) => response.json()).then(updatePortalSyncWidgets).catch(() => {
        // La plataforma local sigue operando aunque no haya internet o portal remoto.
    });
};

if (portalSyncUrl && csrfToken) {
    window.setTimeout(() => {
        runPortalSync(false);
    }, 15000);

    window.setInterval(() => {
        runPortalSync(false);
    }, portalSyncInterval * 60000);
}

document.querySelectorAll('[data-portal-sync-now]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = 'Sincronizando...';
        Promise.resolve(runPortalSync(true)).finally(() => {
            window.setTimeout(() => {
                button.disabled = false;
                button.textContent = originalText;
            }, 900);
        });
    });
});
