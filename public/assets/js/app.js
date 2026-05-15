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

const closeModalElement = (modal) => {
    if (!modal) {
        return;
    }

    if (modal.matches('[data-modal]')) {
        modal.remove();
        return;
    }

    modal.classList.remove('is-visible');
};

document.querySelectorAll('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = button.closest('[data-modal]');
        closeModalElement(modal);
    });
});

document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) {
        return;
    }

    const backdrop = event.target.closest('[data-app-modal-id], [data-modal], #loadingOverlay');
    if (backdrop && event.target === backdrop && (backdrop.classList.contains('is-visible') || backdrop.matches('[data-modal]'))) {
        event.preventDefault();
        closeModalElement(backdrop);
        return;
    }

    const openButton = event.target.closest('[data-open-modal]');
    if (openButton) {
        const targetId = openButton.getAttribute('data-open-modal');
        const modal = document.querySelector(`[data-app-modal-id="${targetId}"]`);
        if (modal) {
            event.preventDefault();
            modal.classList.add('is-visible');
        }
        return;
    }

    const closeButton = event.target.closest('[data-close-app-modal]');
    if (closeButton) {
        const modal = closeButton.closest('[data-app-modal-id]');
        event.preventDefault();
        closeModalElement(modal);
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    const visibleModals = Array.from(document.querySelectorAll('[data-app-modal-id].is-visible, [data-modal], #loadingOverlay.is-visible'));
    const modal = visibleModals.pop();
    closeModalElement(modal);
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

const monthlyPage = document.querySelector('[data-monthly-page]');

if (monthlyPage) {
    const monthlyUrl = monthlyPage.getAttribute('data-monthly-url');
    const monthlyBoard = document.getElementById('monthlyBoard');
    const monthlyModalSlot = document.getElementById('monthlyModalSlot');
    const monthlySearchForm = monthlyPage.querySelector('[data-monthly-search-form]');
    const monthlySearchInput = monthlySearchForm?.querySelector('input[name="search"]');
    const monthlyStatusInput = monthlySearchForm?.querySelector('input[name="status"]');
    const isMonthlyElement = (element) => monthlyPage.contains(element)
        || monthlyBoard?.contains(element)
        || monthlyModalSlot?.contains(element);

    const setMonthlyLoading = (isLoading) => {
        monthlyPage.classList.toggle('is-loading', isLoading);
        if (monthlySearchForm) {
            monthlySearchForm.querySelectorAll('button, input, select').forEach((control) => {
                control.disabled = isLoading;
            });
        }
    };

    const monthlyParamsFromUrl = (url) => {
        const targetUrl = new URL(url, window.location.origin);
        return new URLSearchParams(targetUrl.search);
    };

    const syncMonthlyForm = (params) => {
        if (monthlyStatusInput) {
            monthlyStatusInput.value = params.get('status') || 'all';
        }
        if (monthlySearchInput) {
            monthlySearchInput.value = params.get('search') || '';
        }
    };

    const loadMonthly = (params, options = {}) => {
        if (!monthlyUrl || !monthlyBoard || !monthlyModalSlot) {
            return Promise.resolve();
        }

        const cleanParams = new URLSearchParams(params);
        ['search', 'status', 'membership', 'activity_month', 'memberships_page'].forEach((key) => {
            if ((cleanParams.get(key) || '') === '') {
                cleanParams.delete(key);
            }
        });

        const requestUrl = `${monthlyUrl}?${cleanParams.toString()}`;
        setMonthlyLoading(true);

        return fetch(requestUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('monthly-request-failed');
                }
                return response.json();
            })
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error('monthly-response-invalid');
                }

                monthlyBoard.innerHTML = payload.board || '';
                monthlyModalSlot.innerHTML = payload.detail || '';
                syncMonthlyForm(cleanParams);

                if (options.updateHistory !== false) {
                    const pageUrl = `${window.location.pathname}${cleanParams.toString() ? `?${cleanParams.toString()}` : ''}`;
                    window.history.pushState({ monthly: true }, '', pageUrl);
                }

                if (options.openDetail || payload.selected_id) {
                    document.querySelector('[data-app-modal-id="monthlyDetailModal"]')?.classList.add('is-visible');
                }
                if (options.openActivity) {
                    document.querySelector('[data-app-modal-id="monthlyActivityModal"]')?.classList.add('is-visible');
                }
            })
            .catch(() => {
                window.location.href = `${window.location.pathname}?${cleanParams.toString()}`;
            })
            .finally(() => setMonthlyLoading(false));
    };

    monthlySearchForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        const formData = new FormData(monthlySearchForm);
        const params = new URLSearchParams(formData);
        params.delete('memberships_page');
        loadMonthly(params, { openDetail: true });
    });

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const filterLink = event.target.closest('[data-monthly-filter]');
        if (filterLink && isMonthlyElement(filterLink)) {
            event.preventDefault();
            const params = monthlyParamsFromUrl(filterLink.getAttribute('href') || window.location.href);
            params.set('status', filterLink.getAttribute('data-status') || params.get('status') || 'all');
            params.set('search', monthlySearchInput?.value || params.get('search') || '');
            params.delete('membership');
            params.delete('memberships_page');
            loadMonthly(params);
            return;
        }

        const detailLink = event.target.closest('[data-monthly-detail]');
        if (detailLink && isMonthlyElement(detailLink)) {
            event.preventDefault();
            const params = monthlyParamsFromUrl(detailLink.getAttribute('href') || window.location.href);
            params.set('membership', detailLink.getAttribute('data-membership') || params.get('membership') || '');
            params.set('status', monthlyStatusInput?.value || params.get('status') || 'all');
            params.set('search', monthlySearchInput?.value || params.get('search') || '');
            loadMonthly(params, { openDetail: true });
            return;
        }

        const pageLink = event.target.closest('#monthlyBoard .app-pagination a');
        if (pageLink) {
            event.preventDefault();
            loadMonthly(monthlyParamsFromUrl(pageLink.getAttribute('href') || window.location.href));
        }
    });

    document.addEventListener('submit', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const activityForm = event.target.closest('[data-monthly-activity-form]');
        if (!activityForm) {
            return;
        }

        event.preventDefault();
        loadMonthly(new URLSearchParams(new FormData(activityForm)), {
            openDetail: true,
            openActivity: true,
        });
    });

    window.addEventListener('popstate', () => {
        loadMonthly(new URLSearchParams(window.location.search), { updateHistory: false });
    });
}

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
