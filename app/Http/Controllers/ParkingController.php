<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ParkingTicket;
use App\Models\Payment;
use App\Models\PortalSyncJob;
use App\Models\Site;
use App\Models\TariffProfile;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ParkingController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();
        $scopedTickets = $this->scopedTickets();
        $scopedPayments = $this->scopedPayments();

        $todayEntries = (clone $scopedTickets)->whereDate('entry_time', today())->count();
        $activeCount = (clone $scopedTickets)->where('status', 'active')->count();
        $activeTickets = (clone $scopedTickets)->where('status', 'active')->with(['tariffProfile', 'portalSyncJob'])->latest('entry_time')->take(6)->get();
        $pendingCount = (clone $scopedTickets)->where('status', 'pending_payment')->count();
        $todayIncome = (clone $scopedPayments)->whereDate('paid_at', today())->where('status', 'paid')->sum('total');
        $pendingAmount = (clone $scopedPayments)->where('status', 'pending')->sum('total');
        $site = $user->site ?: Site::query()->first();
        $capacity = max($site?->capacity ?? 1, 1);
        $capacityUsed = $activeCount;
        $capacityPercent = min((int) round(($capacityUsed / $capacity) * 100), 100);
        $todayExitCount = (clone $scopedTickets)->whereDate('exit_time', today())->count();
        $longStayCount = (clone $scopedTickets)->where('entry_time', '<=', now()->subHours(8))->where('status', 'active')->count();
        $activeLockerCount = (clone $scopedTickets)->where('status', 'active')->where('uses_locker', true)->count();
        $todayLockerCount = (clone $scopedTickets)->whereDate('entry_time', today())->where('uses_locker', true)->count();
        $todayLockerIncome = (clone $scopedTickets)->whereDate('entry_time', today())->where('uses_locker', true)->sum('locker_fee');
        $alerts = collect([
            $pendingCount > 0 ? $pendingCount . ' pagos pendientes por cerrar.' : null,
            $capacityUsed >= $capacity ? 'Capacidad al maximo.' : null,
            $longStayCount > 0 ? 'Hay vehiculos con estancias largas.' : null,
        ])->filter()->values();

        $revenueTrend = collect(range(6, 18, 2))->map(function (int $hour) use ($scopedPayments) {
            $sum = (clone $scopedPayments)
                ->whereDate('paid_at', today())
                ->whereBetween('paid_at', [
                    today()->startOfDay(),
                    today()->copy()->setHour($hour)->endOfHour()
                ])
                ->sum('total');

            return ['label' => sprintf('%02d:00', $hour), 'value' => $sum];
        });

        $movementTrend = collect(range(6, 20, 2))->map(function (int $hour) use ($scopedTickets) {
            $from = today()->copy()->setHour($hour)->startOfHour();
            $to = today()->copy()->setHour($hour)->endOfHour();

            return [
                'label' => sprintf('%02d:00', $hour),
                'entries' => (clone $scopedTickets)->whereBetween('entry_time', [$from, $to])->count(),
                'exits' => (clone $scopedTickets)->whereBetween('exit_time', [$from, $to])->count(),
            ];
        });

        $occupancyTrend = collect(range(6, 20, 2))->map(function (int $hour) use ($scopedTickets) {
            $moment = today()->copy()->setHour($hour)->endOfHour();
            $value = (clone $scopedTickets)
                ->where('entry_time', '<=', $moment)
                ->where(function (Builder $query) use ($moment): void {
                    $query->whereNull('exit_time')->orWhere('exit_time', '>=', $moment);
                })
                ->count();

            return ['label' => sprintf('%02d:00', $hour), 'value' => $value];
        });

        $vehicleCounts = (clone $scopedTickets)
            ->where('status', 'active')
            ->select('vehicle_type', DB::raw('count(*) as total'))
            ->groupBy('vehicle_type')
            ->pluck('total', 'vehicle_type');

        $vehicleMix = collect([
            ['key' => 'moto', 'label' => 'Moto', 'value' => (int) $vehicleCounts->get('moto', 0), 'color' => '#ff7a1a'],
            ['key' => 'auto', 'label' => 'Carro', 'value' => (int) $vehicleCounts->get('auto', 0), 'color' => '#0f766e'],
            ['key' => 'bicicleta', 'label' => 'Bici', 'value' => (int) $vehicleCounts->get('bicicleta', 0), 'color' => '#2563eb'],
        ]);

        $hoursElapsed = max(now()->diffInHours(today()->copy()->setHour(6)), 1);
        $dailyTarget = max((int) ceil(max($todayIncome + $pendingAmount, 100000) / 50000) * 50000, 100000);
        $goalProgress = min((int) round(($todayIncome / $dailyTarget) * 100), 100);
        $recentMovements = (clone $scopedTickets)
            ->with(['payment', 'tariffProfile'])
            ->where(function (Builder $query): void {
                $query->whereDate('entry_time', today())->orWhereDate('exit_time', today());
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('pages.dashboard', $this->sharedData([
            'pageTitle' => 'Dashboard principal',
            'pageSubtitle' => 'Operacion diaria del parqueadero en tiempo real.',
            'stats' => [
                ['label' => 'Vehiculos activos', 'value' => $capacityUsed, 'meta' => 'En parqueadero'],
                ['label' => 'Entradas hoy', 'value' => $todayEntries, 'meta' => 'Movimientos del dia'],
                ['label' => 'Pagos pendientes', 'value' => $pendingCount, 'meta' => 'Por recaudar'],
                ['label' => 'Capacidad', 'value' => $capacityPercent . '%', 'meta' => $capacityUsed . '/' . $capacity . ' ocupados'],
            ],
            'financeStat' => $user->isAdmin() ? $this->money($todayIncome) : null,
            'todayIncomeRaw' => (int) $todayIncome,
            'dailyTarget' => $dailyTarget,
            'goalProgress' => $goalProgress,
            'pendingAmount' => (int) $pendingAmount,
            'activeTickets' => $activeTickets,
            'alerts' => $alerts,
            'revenueTrend' => $revenueTrend,
            'movementTrend' => $movementTrend,
            'occupancyTrend' => $occupancyTrend,
            'vehicleMix' => $vehicleMix,
            'capacityPercent' => $capacityPercent,
            'capacityUsed' => $capacityUsed,
            'capacity' => $capacity,
            'pendingPayments' => $this->pendingPaymentsData(),
            'todayExitCount' => $todayExitCount,
            'averageStay' => $this->averageStayMinutes(clone $scopedTickets),
            'criticalStats' => [
                'entryRate' => number_format($todayEntries / $hoursElapsed, 1),
                'exitRate' => number_format($todayExitCount / $hoursElapsed, 1),
                'alertRate' => $activeCount > 0 ? number_format(($longStayCount / $activeCount) * 100, 1) : '0.0',
            ],
            'portalSync' => $this->portalSyncOverview(),
            'lockerStats' => [
                'active' => $activeLockerCount,
                'today' => $todayLockerCount,
                'income' => (int) $todayLockerIncome,
                'fee' => (int) ($site?->locker_fee ?? 0),
            ],
            'longStayCount' => $longStayCount,
            'recentMovements' => $recentMovements,
        ]));
    }

    public function manage(Request $request): View|RedirectResponse
    {
        $exitLookup = Str::upper(trim((string) $request->query('lookup', '')));
        if ($exitLookup !== '') {
            $selectedTicket = $this->ticketLookupQuery($exitLookup)->first();
            if ($selectedTicket) {
                return redirect()->route('transaction.show', $selectedTicket);
            }

            return redirect()->route('manage')->with('modal', [
                'type' => 'warning',
                'title' => 'Sin resultados',
                'message' => 'No encontramos un ticket con ese dato de busqueda.',
            ]);
        }

        $activeTickets = $this->scopedTickets()
            ->with(['tariffProfile', 'portalSyncJob'])
            ->where('status', 'active')
            ->latest('entry_time')
            ->paginate(10, ['*'], 'active_page')
            ->withQueryString();

        $recentClosedTickets = $this->scopedTickets()
            ->with(['payment', 'portalSyncJob'])
            ->whereIn('status', ['paid', 'pending_payment'])
            ->latest('updated_at')
            ->paginate(10, ['*'], 'closed_page')
            ->withQueryString();

        $pendingPayments = $this->scopedPayments()
            ->with('ticket.portalSyncJob')
            ->where('status', 'pending')
            ->latest('updated_at')
            ->paginate(8, ['*'], 'pending_page')
            ->withQueryString();

        return view('pages.manage', $this->sharedData([
            'pageTitle' => 'Gestion unificada',
            'pageSubtitle' => 'Centro 360 para controlar entradas, salidas y pagos pendientes.',
            'overviewStats' => [
                'active' => $this->scopedTickets()->where('status', 'active')->count(),
                'pending' => $this->scopedTickets()->where('status', 'pending_payment')->count(),
                'todayEntries' => $this->scopedTickets()->whereDate('entry_time', today())->count(),
                'todayClosed' => $this->scopedTickets()->whereDate('updated_at', today())->whereIn('status', ['paid', 'pending_payment'])->count(),
            ],
            'activeTickets' => $activeTickets,
            'recentClosedTickets' => $recentClosedTickets,
            'pendingPayments' => $pendingPayments,
        ]));
    }

    public function entry(Request $request): View|RedirectResponse
    {
        $plateLookup = Str::upper(str_replace(' ', '', trim((string) $request->query('plate_lookup', ''))));
        if ($plateLookup !== '') {
            $selectedTicket = $this->ticketLookupQuery($plateLookup)
                ->whereIn('status', ['active', 'pending_payment'])
                ->first();

            if (! $selectedTicket) {
                $selectedTicket = $this->scopedTickets()
                    ->where(function (Builder $query) use ($plateLookup): void {
                        $query->where('ticket_code', $plateLookup)
                            ->orWhere('barcode', $plateLookup);
                    })
                    ->latest('entry_time')
                    ->first();
            }

            if ($selectedTicket) {
                return redirect()->route('transaction.show', $selectedTicket);
            }
        }

        $prefillTicket = $plateLookup !== ''
            ? $this->scopedTickets()->where('plate', $plateLookup)->latest('entry_time')->first()
            : null;
        $activeTicket = $plateLookup !== ''
            ? $this->scopedTickets()->where('plate', $plateLookup)->whereIn('status', ['active', 'pending_payment'])->latest('entry_time')->first()
            : null;

        return view('pages.entry', $this->sharedData([
            'pageTitle' => 'Registrar entrada',
            'pageSubtitle' => 'Ingreso agil para motos, autos y bicicletas con captura rapida.',
            'tariffs' => TariffProfile::query()
                ->where('active', true)
                ->whereIn('tariff_type', ['normal', 'convenio'])
                ->orderBy('vehicle_type')
                ->orderBy('name')
                ->get(),
            'prefillTicket' => $prefillTicket,
            'activeTicket' => $activeTicket,
            'plateLookup' => $plateLookup,
            'defaultVehicleType' => 'moto',
            'nextTicketCode' => $this->nextTicketCode(),
            'lockerFee' => (int) ((auth()->user()?->site ?: Site::query()->first())?->locker_fee ?? 0),
        ]));
    }

    public function storeEntry(Request $request): RedirectResponse
    {
        $lookup = Str::upper(str_replace(' ', '', trim((string) $request->input('plate', ''))));
        if ($lookup !== '') {
            $selectedTicket = $this->ticketLookupQuery($lookup)
                ->whereIn('status', ['active', 'pending_payment'])
                ->first();

            if (! $selectedTicket) {
                $selectedTicket = $this->scopedTickets()
                    ->where(function (Builder $query) use ($lookup): void {
                        $query->where('ticket_code', $lookup)
                            ->orWhere('barcode', $lookup);
                    })
                    ->latest('entry_time')
                    ->first();
            }

            if ($selectedTicket) {
                return redirect()->route('transaction.show', $selectedTicket)
                    ->with('modal', [
                        'type' => 'warning',
                        'title' => 'Ticket encontrado',
                        'message' => 'Ya existe un ticket con ese dato. Te llevamos a la vista de salida.',
                    ]);
            }
        }

        $data = $request->validate([
            'plate' => ['required', 'string', 'max:30'],
            'vehicle_type' => ['required', 'in:moto,auto,bicicleta'],
            'tariff_profile_id' => ['required', 'exists:tariff_profiles,id'],
            'location_number' => ['required', 'integer', 'min:1'],
            'uses_locker' => ['nullable', 'boolean'],
            'locker_number' => ['nullable', 'required_if:uses_locker,1', 'string', 'max:40'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $plate = Str::upper(str_replace(' ', '', $data['plate']));
        if (strlen($plate) > 12) {
            return back()->withInput()->with('modal', [
                'type' => 'warning',
                'title' => 'Dato no valido para entrada',
                'message' => 'Para crear una entrada nueva escribe una placa de maximo 12 caracteres. Los codigos de ticket se usan para buscar salidas.',
            ]);
        }

        $activeTicket = $this->scopedTickets()
            ->where('plate', $plate)
            ->whereIn('status', ['active', 'pending_payment'])
            ->first();

        if ($activeTicket) {
            return redirect()->route('transaction.show', $activeTicket)
                ->with('modal', [
                    'type' => 'warning',
                    'title' => 'Placa ya registrada',
                    'message' => 'La placa ya tiene un ticket activo o pendiente. Te llevamos al detalle para continuar la salida.',
                ]);
        }

        $latestHistory = ParkingTicket::query()->where('plate', $plate)->latest('entry_time')->first();
        $ticketCode = $this->nextTicketCode();
        $siteId = auth()->user()->site_id ?? Site::query()->value('id');
        $site = Site::query()->find($siteId) ?: Site::query()->first();
        $usesLocker = (bool) ($data['uses_locker'] ?? false);

        $ticket = ParkingTicket::create([
            'site_id' => $site?->id,
            'tariff_profile_id' => $data['tariff_profile_id'],
            'ticket_code' => $ticketCode,
            'barcode' => $ticketCode,
            'plate' => $plate,
            'vehicle_type' => $data['vehicle_type'],
            'status' => 'active',
            'location_number' => $data['location_number'],
            'uses_locker' => $usesLocker,
            'locker_number' => $usesLocker ? Str::upper(trim((string) ($data['locker_number'] ?? ''))) : null,
            'locker_fee' => $usesLocker ? (int) ($site?->locker_fee ?? 0) : 0,
            'customer_name' => $data['customer_name'] ?: $latestHistory?->customer_name,
            'customer_phone' => $data['customer_phone'] ?: $latestHistory?->customer_phone,
            'created_by' => auth()->id(),
            'closed_by' => null,
            'entry_time' => now(),
            'exit_time' => null,
            'notes' => $data['notes'],
            'is_lost_ticket' => false,
        ]);

        $this->logAction('entrada', 'ticket', 'Ticket ' . $ticket->ticket_code . ' creado para ' . $ticket->plate, $ticket, [
            'tarifa' => $ticket->tariffProfile?->name,
            'ubicacion' => $ticket->location_number,
            'locker' => $ticket->uses_locker ? $ticket->locker_number : 'no',
        ]);
        $this->syncPortalTicket($ticket->fresh(['site', 'tariffProfile', 'payment']), 'entrada');

        if ($request->boolean('send_whatsapp')) {
            return redirect()->away($this->whatsappReceiptUrl($ticket->fresh(['site', 'tariffProfile', 'payment']), 'ingreso'));
        }

        if (($request->input('print_mode', '1')) === '0') {
            return redirect()->route('entry')
                ->with('modal', [
                    'type' => 'success',
                    'title' => 'Entrada registrada',
                    'message' => 'Ticket ' . $ticket->ticket_code . ' creado correctamente.',
                ]);
        }

        return redirect()->route('tickets.print', [
            'ticket' => $ticket,
            'type' => 'ingreso',
        ])
            ->with('modal', [
                'type' => 'success',
                'title' => 'Entrada registrada',
                'message' => 'Ticket ' . $ticket->ticket_code . ' creado correctamente.',
            ]);
    }

    public function closeTicket(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ticket_id' => ['required', 'exists:parking_tickets,id'],
            'payment_method' => ['required', 'in:efectivo,nequi,pending'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'mark_lost_ticket' => ['nullable', 'boolean'],
        ]);

        /** @var ParkingTicket $ticket */
        $ticket = $this->scopedTickets()->with(['tariffProfile', 'payment'])->findOrFail($data['ticket_id']);

        if (! in_array($ticket->status, ['active', 'pending_payment'], true)) {
            return redirect()->route('transaction.show', $ticket)
                ->with('modal', [
                    'type' => 'warning',
                    'title' => 'Ticket cerrado',
                    'message' => 'Este ticket ya fue cerrado anteriormente.',
                ]);
        }

        $ticket->is_lost_ticket = (bool) ($data['mark_lost_ticket'] ?? false);
        $ticket->exit_time = now();
        $ticket->closed_by = auth()->id();

        $summary = $this->calculateTicket($ticket);
        $method = $data['payment_method'];
        $received = (int) round((float) ($data['received_amount'] ?? 0));
        $status = $method === 'pending' ? 'pending' : 'paid';

        DB::transaction(function () use ($ticket, $summary, $method, $received, $status, $data): void {
            $ticket->status = $status === 'paid' ? 'paid' : 'pending_payment';
            $ticket->notes = trim(($ticket->notes ? $ticket->notes . ' | ' : '') . ($data['notes'] ?? ''));
            $ticket->save();

            Payment::updateOrCreate(
                ['parking_ticket_id' => $ticket->id],
                [
                    'user_id' => auth()->id(),
                    'method' => $method,
                    'subtotal' => $summary['subtotal'],
                    'discount' => $summary['discount'],
                    'surcharge' => $summary['surcharge'],
                    'tax' => $summary['tax'],
                    'total' => $summary['total'],
                    'received_amount' => $status === 'paid' ? max($received, $summary['total']) : 0,
                    'change_amount' => $status === 'paid' ? max(max($received, $summary['total']) - $summary['total'], 0) : 0,
                    'paid_at' => $status === 'paid' ? now() : null,
                    'status' => $status,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            $this->logAction(
                $status === 'paid' ? 'salida_confirmada' : 'pago_pendiente',
                'pagos',
                'Ticket ' . $ticket->ticket_code . ' procesado con metodo ' . strtoupper($method),
                $ticket,
                $summary
            );
        });
        $this->syncPortalTicket($ticket->fresh(['site', 'tariffProfile', 'payment']), $status === 'paid' ? 'salida' : 'pago_pendiente', $summary);

        if ($request->boolean('send_whatsapp')) {
            return redirect()->away($this->whatsappReceiptUrl($ticket->fresh(['site', 'tariffProfile', 'payment']), 'salida'));
        }

        return redirect()->route('tickets.print', [
            'ticket' => $ticket,
            'type' => 'salida',
        ])
            ->with('modal', [
                'type' => $status === 'paid' ? 'success' : 'warning',
                'title' => $status === 'paid' ? 'Salida confirmada' : 'Pago pendiente',
                'message' => $status === 'paid'
                    ? 'Pago confirmado para ' . $ticket->plate . '.'
                    : 'El ticket quedo marcado como pendiente por pagar.',
            ]);
    }

    public function settlePending(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:efectivo,nequi'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        abort_unless($payment->status === 'pending', 404);
        $ticket = $payment->ticket()->with('tariffProfile')->firstOrFail();
        abort_unless($this->canAccessTicket($ticket), 403);

        $received = (int) round((float) ($data['received_amount'] ?? $payment->total));

        DB::transaction(function () use ($payment, $ticket, $data, $received): void {
            $payment->update([
                'method' => $data['payment_method'],
                'received_amount' => max($received, $payment->total),
                'change_amount' => max(max($received, $payment->total) - $payment->total, 0),
                'paid_at' => now(),
                'status' => 'paid',
            ]);

            $ticket->update([
                'status' => 'paid',
                'closed_by' => auth()->id(),
                'exit_time' => $ticket->exit_time ?: now(),
            ]);

            $this->logAction('pago_regularizado', 'pagos', 'Pago pendiente regularizado para ' . $ticket->ticket_code, $ticket, [
                'metodo' => $data['payment_method'],
                'total' => $payment->total,
            ]);
        });
        $this->syncPortalTicket($ticket->fresh(['site', 'tariffProfile', 'payment']), 'pago_regularizado');

        return redirect()->route('tickets.print', [
            'ticket' => $ticket,
            'type' => 'salida',
        ])
            ->with('modal', [
                'type' => 'success',
                'title' => 'Pago actualizado',
                'message' => 'El pago pendiente fue confirmado correctamente.',
            ]);
    }

    public function reports(): View
    {
        $this->authorizeAdmin();

        $dateFrom = request('date_from') ? Carbon::parse((string) request('date_from'))->startOfDay() : today()->startOfDay();
        $dateTo = request('date_to') ? Carbon::parse((string) request('date_to'))->endOfDay() : today()->endOfDay();

        $ticketsQuery = ParkingTicket::query()
            ->with(['payment', 'creator', 'closer'])
            ->whereBetween('entry_time', [$dateFrom, $dateTo])
            ->when(request('vehicle_type'), fn(Builder $query, string $type) => $query->where('vehicle_type', $type))
            ->when(request('status'), fn(Builder $query, string $status) => $query->where('status', $status))
            ->when(request('payment_method'), function (Builder $query, string $method): void {
                $query->whereHas('payment', fn(Builder $payment) => $payment->where('method', $method));
            })
            ->when(request('search'), function (Builder $query, string $search) {
                $term = Str::upper(trim($search));
                $query->where(function (Builder $inner) use ($term) {
                    $inner->where('ticket_code', 'like', '%' . $term . '%')
                        ->orWhere('plate', 'like', '%' . $term . '%')
                        ->orWhere('barcode', 'like', '%' . $term . '%');
                });
            });

        $paymentsQuery = Payment::query()
            ->with('ticket')
            ->whereHas('ticket', function (Builder $query) use ($dateFrom, $dateTo): void {
                $query->whereBetween('entry_time', [$dateFrom, $dateTo])
                    ->when(request('vehicle_type'), fn(Builder $inner, string $type) => $inner->where('vehicle_type', $type))
                    ->when(request('status'), fn(Builder $inner, string $status) => $inner->where('status', $status))
                    ->when(request('search'), function (Builder $inner, string $search) {
                        $term = Str::upper(trim($search));
                        $inner->where(function (Builder $lookup) use ($term) {
                            $lookup->where('ticket_code', 'like', '%' . $term . '%')
                                ->orWhere('plate', 'like', '%' . $term . '%')
                                ->orWhere('barcode', 'like', '%' . $term . '%');
                        });
                    });
            })
            ->when(request('payment_method'), fn(Builder $query, string $method) => $query->where('method', $method));

        $payments = (clone $paymentsQuery)->where('status', 'paid')->get();
        $tickets = (clone $ticketsQuery)->get();
        $ticketsByType = $tickets->groupBy('vehicle_type')->map->count();
        $groupBy = in_array(request('group_by'), ['dia', 'vehiculo'], true) ? request('group_by') : 'dia';
        $groupedRows = $groupBy === 'vehiculo'
            ? $tickets->groupBy('vehicle_type')->map(function (Collection $items, string $vehicleType) {
                return [
                    'label' => ucfirst($vehicleType ?: 'Sin tipo'),
                    'entries' => $items->count(),
                    'exits' => $items->filter(fn(ParkingTicket $ticket) => $ticket->exit_time)->count(),
                    'active' => $items->where('status', 'active')->count(),
                    'pending' => $items->where('status', 'pending_payment')->count(),
                    'income' => $items->sum(fn(ParkingTicket $ticket) => (int) ($ticket->payment?->total ?? 0)),
                ];
            })->values()
            : $tickets->groupBy(fn(ParkingTicket $ticket) => optional($ticket->entry_time)->format('Y-m-d') ?: 'Sin fecha')
                ->map(function (Collection $items, string $date) {
                    return [
                        'label' => $date === 'Sin fecha' ? $date : Carbon::parse($date)->format('d/m/Y'),
                        'entries' => $items->count(),
                        'exits' => $items->filter(fn(ParkingTicket $ticket) => $ticket->exit_time)->count(),
                        'active' => $items->where('status', 'active')->count(),
                        'pending' => $items->where('status', 'pending_payment')->count(),
                        'income' => $items->sum(fn(ParkingTicket $ticket) => (int) ($ticket->payment?->total ?? 0)),
                    ];
                })->values();
        $hours = collect(range(6, 20, 2))->map(function (int $hour) use ($payments) {
            $value = $payments->filter(fn(Payment $payment) => $payment->paid_at && (int) $payment->paid_at->format('H') === $hour)->sum('total');
            return ['label' => sprintf('%02d:00', $hour), 'value' => $value];
        });
        $daily = collect(range(6, 0))->reverse()->map(function (int $days) use ($payments, $dateTo) {
            $date = $dateTo->copy()->startOfDay()->subDays($days);
            return [
                'label' => $date->format('d M'),
                'value' => $payments->filter(fn(Payment $payment) => $payment->paid_at?->isSameDay($date))->sum('total'),
            ];
        });

        $transactions = (clone $ticketsQuery)->latest('updated_at')->paginate(10, ['*'], 'transactions_page')->withQueryString();

        return view('pages.reports', $this->sharedData([
            'pageTitle' => 'Centro de reportes',
            'pageSubtitle' => 'Resumen financiero, uso y trazabilidad del parqueadero.',
            'reportStats' => [
                'income' => $this->money($payments->sum('total')),
                'transactions' => $payments->count(),
                'pending' => (clone $paymentsQuery)->where('status', 'pending')->count(),
                'vehicles' => $tickets->count(),
                'entries' => $tickets->count(),
                'exits' => $tickets->filter(fn(ParkingTicket $ticket) => $ticket->exit_time)->count(),
            ],
            'hourlyData' => $hours,
            'dailyData' => $daily,
            'vehicleMix' => [
                ['label' => 'Motos', 'value' => $ticketsByType->get('moto', 0), 'class' => 'legend-moto'],
                ['label' => 'Autos', 'value' => $ticketsByType->get('auto', 0), 'class' => 'legend-car'],
                ['label' => 'Bicicletas', 'value' => $ticketsByType->get('bicicleta', 0), 'class' => 'legend-bike'],
            ],
            'paymentMix' => [
                ['label' => 'Efectivo', 'value' => $payments->where('method', 'efectivo')->count(), 'class' => 'legend-cash'],
                ['label' => 'Nequi', 'value' => $payments->where('method', 'nequi')->count(), 'class' => 'legend-nequi'],
                ['label' => 'Pendiente', 'value' => (clone $paymentsQuery)->where('status', 'pending')->count(), 'class' => 'legend-pending'],
            ],
            'groupBy' => $groupBy,
            'groupedRows' => $groupedRows,
            'transactions' => $transactions,
            'filters' => request()->only(['date_from', 'date_to', 'vehicle_type', 'payment_method', 'status', 'search', 'group_by']),
            'entriesToday' => $tickets->sortByDesc('entry_time')->take(8),
            'exitsToday' => $tickets->filter(fn(ParkingTicket $ticket) => $ticket->exit_time)->sortByDesc('exit_time')->take(8),
        ]));
    }

    public function settings(Request $request): View
    {
        $this->authorizeAdmin();
        $selected = TariffProfile::query()
            ->when($request->query('tariff'), fn(Builder $query, string $tariff) => $query->where('id', $tariff))
            ->first() ?? TariffProfile::query()->orderBy('name')->first();

        $tariffs = TariffProfile::query()
            ->orderBy('name')
            ->paginate(10, ['*'], 'tariffs_page')
            ->withQueryString();

        return view('pages.settings', $this->sharedData([
            'pageTitle' => 'Configuracion',
            'pageSubtitle' => 'Tarifas y reglas operativas del parqueadero.',
            'tariffs' => $tariffs,
            'selectedTariff' => $selected,
            'currentSite' => auth()->user()?->site ?: Site::query()->first(),
            'recentAudits' => AuditLog::query()->latest('logged_at')->take(12)->get(),
            'strategyOptions' => [
                'fraction' => 'Tarifa por minuto',
                'fixed' => 'Tarifa plena',
            ],
            'strategyHelp' => [
                'fraction' => 'Cobra un valor por cada minuto de permanencia.',
                'fixed' => 'Cobra un valor fijo cuando se cumple la regla configurada.',
            ],
            'chargeUnitOptions' => [
                'minute' => 'Minutos',
            ],
        ]));
    }

    public function updateLockerSettings(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'locker_fee' => ['required', 'integer', 'min:0'],
        ]);

        $site = auth()->user()?->site ?: Site::query()->firstOrFail();
        $site->update(['locker_fee' => (int) $data['locker_fee']]);

        $this->logAction('locker_actualizado', 'configuracion', 'Tarifa fija de locker actualizada a ' . $this->money((int) $data['locker_fee']), $site, [
            'locker_fee' => (int) $data['locker_fee'],
        ]);

        return redirect()->route('settings')
            ->with('modal', [
                'type' => 'success',
                'title' => 'Locker actualizado',
                'message' => 'La tarifa fija del locker fue guardada correctamente.',
            ]);
    }

    public function storeTariff(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $this->validateTariff($request);

        if ($data['type'] === 'plena') {
            $existingFullRate = TariffProfile::query()
                ->where('vehicle_type', $data['vehicle_type'])
                ->where('tariff_type', 'plena')
                ->exists();

            if ($existingFullRate) {
                return back()->withInput()->with('modal', [
                    'type' => 'warning',
                    'title' => 'Tarifa plena existente',
                    'message' => 'Ya existe una tarifa plena para este tipo de vehiculo.',
                ]);
            }
        }

        $tariff = TariffProfile::create($this->mapTariffPayload($data));

        $this->logAction('tarifa_creada', 'configuracion', 'Tarifa ' . $tariff->name . ' creada', $tariff, $data);

        return redirect()->route('settings', ['tariff' => $tariff->id])
            ->with('modal', [
                'type' => 'success',
                'title' => 'Tarifa creada',
                'message' => 'La nueva tarifa fue creada correctamente.',
            ]);
    }

    public function updateTariff(Request $request, TariffProfile $tariff): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $this->validateTariff($request);

        if ($data['type'] === 'plena') {
            $existingFullRate = TariffProfile::query()
                ->where('vehicle_type', $data['vehicle_type'])
                ->where('tariff_type', 'plena')
                ->whereKeyNot($tariff->id)
                ->exists();

            if ($existingFullRate) {
                return back()->withInput()->with('modal', [
                    'type' => 'warning',
                    'title' => 'Tarifa plena existente',
                    'message' => 'Solo puede existir una tarifa plena por tipo de vehiculo.',
                ]);
            }
        }

        $tariff->update($this->mapTariffPayload($data));

        $this->logAction('tarifa_actualizada', 'configuracion', 'Tarifa ' . $tariff->name . ' actualizada', $tariff, $data);

        return redirect()->route('settings', ['tariff' => $tariff->id])
            ->with('modal', [
                'type' => 'success',
                'title' => 'Tarifa guardada',
                'message' => 'La configuracion de tarifa se actualizo correctamente.',
            ]);
    }

    public function audit(): View
    {
        $this->authorizeAdmin();
        $selectedUser = User::query()
            ->when(request('user'), fn(Builder $query, string $userId) => $query->where('id', $userId))
            ->first() ?? User::query()->orderBy('name')->first();

        return view('pages.audit', $this->sharedData([
            'pageTitle' => 'Auditoria y usuarios',
            'pageSubtitle' => 'Control de operarios, administradores y cambios.',
            'users' => User::query()->with('site')->orderBy('name')->paginate(10, ['*'], 'users_page')->withQueryString(),
            'auditLogs' => AuditLog::query()->with('user')->latest('logged_at')->paginate(12, ['*'], 'logs_page')->withQueryString(),
            'selectedUser' => $selectedUser,
        ]));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,operario'],
            'shift_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $siteId = auth()->user()->site_id ?? Site::query()->value('id');
        $user = User::create([
            ...$data,
            'site_id' => $siteId,
            'password' => Hash::make($data['password']),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $this->logAction('usuario_creado', 'usuarios', 'Usuario ' . $user->username . ' creado', $user, [
            'role' => $user->role,
        ]);

        return redirect()->route('audit', ['user' => $user->id])
            ->with('modal', [
                'type' => 'success',
                'title' => 'Usuario creado',
                'message' => 'El usuario fue creado correctamente.',
            ]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,operario'],
            'shift_name' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'shift_name' => $data['shift_name'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        $this->logAction('usuario_actualizado', 'usuarios', 'Usuario ' . $user->username . ' actualizado', $user, $payload);

        return redirect()->route('audit', ['user' => $user->id])
            ->with('modal', [
                'type' => 'success',
                'title' => 'Usuario actualizado',
                'message' => 'Los datos del usuario se guardaron.',
            ]);
    }

    public function transaction(ParkingTicket $ticket): View
    {
        abort_unless($this->canAccessTicket($ticket), 403);
        $ticket->load(['site', 'payment', 'tariffProfile', 'creator', 'closer', 'audits.user', 'portalSyncJob']);

        return view('pages.transaction', $this->sharedData([
            'pageTitle' => 'Detalle de transaccion',
            'pageSubtitle' => 'Ticket ' . $ticket->ticket_code . ' - ' . $ticket->plate,
            'ticket' => $ticket,
            'summary' => $this->calculateTicket($ticket),
            'whatsappReceiptUrls' => [
                'ingreso' => $this->whatsappReceiptUrl($ticket, 'ingreso'),
                'salida' => $this->whatsappReceiptUrl($ticket, 'salida'),
            ],
        ]));
    }

    public function receipt(ParkingTicket $ticket, string $type): View
    {
        abort_unless(in_array($type, ['ingreso', 'salida'], true), 404);
        abort_unless($this->canAccessTicket($ticket), 403);

        return view('pages.receipt', $this->receiptViewData($ticket, $type, [
            'autoPrint' => request()->boolean('auto_print', false),
            'autoReturn' => request()->boolean('auto_return'),
            'autoClose' => false,
            'returnUrl' => $this->receiptReturnUrl($ticket),
        ]));
    }

    public function printReceipt(ParkingTicket $ticket, string $type): View|RedirectResponse
    {
        abort_unless(in_array($type, ['ingreso', 'salida'], true), 404);
        abort_unless($this->canAccessTicket($ticket), 403);

        $printed = false;
        try {
            $printed = $this->sendReceiptToDefaultPrinter($ticket, $type);
        } catch (\Throwable) {
            $printed = false;
        }
        $redirect = request('return_to') === 'transaction'
            ? redirect()->route('transaction.show', $ticket)
            : redirect()->route('entry');

        if ($printed) {
            return $redirect->with('modal', [
                'type' => 'success',
                'title' => 'Impresion enviada',
                'message' => 'El recibo fue enviado a la impresora predeterminada.',
            ]);
        }

        return view('pages.receipt', $this->receiptViewData($ticket, $type, [
            'autoPrint' => true,
            'autoReturn' => true,
            'autoClose' => false,
            'returnUrl' => $this->receiptReturnUrl($ticket),
        ]));
    }

    private function receiptViewData(ParkingTicket $ticket, string $type, array $extra = []): array
    {
        $ticket->load(['site', 'payment', 'tariffProfile', 'creator', 'closer']);
        $summary = $this->calculateTicket($ticket);
        $site = $ticket->site ?: auth()->user()?->site ?: Site::query()->first();

        return $this->sharedData(array_merge([
            'pageTitle' => $type === 'ingreso' ? 'Recibo de ingreso' : 'Recibo de salida',
            'pageSubtitle' => 'Impresion termica del ticket.',
            'ticket' => $ticket,
            'receiptType' => $type,
            'summary' => $summary,
            'site' => $site,
            'receiptBusiness' => [
                'name' => Str::upper('Parqueadero Donde Richard'),
                'phone' => config('app.parking_phone', env('PARKING_PHONE', '3151')),
                'address' => config('app.parking_address', env('PARKING_ADDRESS', 'Calle 57 dsaj')),
            ],
            'barcodeSvg' => $type === 'ingreso' ? $this->code39BarcodeSvg($ticket->ticket_code) : null,
            'formattedDuration' => $this->formatReceiptDuration((int) $summary['minutes']),
            'formattedLocation' => $this->formatReceiptLocation($ticket),
            'whatsappReceiptUrl' => $this->whatsappReceiptUrl($ticket, $type),
            'autoPrint' => false,
            'autoReturn' => false,
            'autoClose' => false,
            'returnUrl' => route('entry'),
        ], $extra));
    }

    private function receiptReturnUrl(ParkingTicket $ticket): string
    {
        return request('return_to') === 'transaction'
            ? route('transaction.show', $ticket)
            : route('entry');
    }

    private function whatsappReceiptUrl(ParkingTicket $ticket, string $type): string
    {
        $ticket->loadMissing(['site', 'payment', 'tariffProfile']);
        $summary = $this->calculateTicket($ticket);
        $businessName = Str::upper('Parqueadero Donde Richard');
        $location = $this->formatReceiptLocation($ticket);
        $locker = $ticket->uses_locker
            ? 'SI - Locker ' . ($ticket->locker_number ?: 'N/A') . ' - ' . $this->money((int) ($summary['locker_fee'] ?? $ticket->locker_fee ?? 0))
            : 'NO';

        $lines = [
            '*' . $businessName . '*',
            '*' . ($type === 'ingreso' ? 'RECIBO DE INGRESO' : 'RECIBO DE SALIDA') . '*',
            '',
            '*Ticket:* ' . $ticket->ticket_code,
            '*Placa:* ' . $ticket->plate,
            '*Ubicacion vehiculo:* ' . $location,
            '*Locker:* ' . $locker,
            '*Entrada:* ' . optional($ticket->entry_time)->format('d/m/Y h:i A'),
        ];

        if ($type === 'salida') {
            $lines = array_merge($lines, [
                '*Salida:* ' . (optional($ticket->exit_time)->format('d/m/Y h:i A') ?: 'Pendiente'),
                '*Tiempo total:* ' . $this->formatReceiptDuration((int) $summary['minutes']),
                '*Tarifa:* ' . Str::upper($summary['applied_tariff']) . ' - ' . Str::upper($summary['pricing_label']),
                '*Parqueo:* ' . $this->money((int) ($summary['parking_subtotal'] ?? $summary['subtotal'])),
                '*Subtotal:* ' . $this->money((int) $summary['subtotal']),
                '*Total:* *' . $this->money((int) $summary['total']) . '*',
                '',
                'Gracias por su visita. Lo esperamos nuevamente.',
            ]);
        } else {
            $lines = array_merge($lines, [
                '*Tarifa:* ' . Str::upper($ticket->tariffProfile?->name ?? 'SIN TARIFA'),
                '',
                'Conserve este comprobante durante su estancia.',
                'Presente este ticket para la salida.',
            ]);
        }

        $phone = preg_replace('/\D+/', '', (string) $ticket->customer_phone);
        if (strlen($phone) === 10 && str_starts_with($phone, '3')) {
            $phone = '57' . $phone;
        }

        $query = ['text' => implode("\n", $lines)];
        if ($phone !== '') {
            $query['phone'] = $phone;
        }

        return 'https://api.whatsapp.com/send?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    public function exit(): RedirectResponse
    {
        return redirect()->route('manage');
    }

    public function payment(): RedirectResponse
    {
        return redirect()->route('manage', ['tab' => 'exit']);
    }

    public function confirmation(): RedirectResponse
    {
        return redirect()->route('manage');
    }

    public function runPortalSync(Request $request): JsonResponse
    {
        $intervalMinutes = $this->portalSyncIntervalMinutes();
        $url = (string) env('PORTAL_SYNC_URL', 'https://ingedev94.com/portalricardo/sync.php');
        if ($url === '') {
            return response()->json([
                'ok' => false,
                'message' => 'PORTAL_SYNC_URL no esta configurado.',
            ], 422);
        }

        $force = $request->boolean('force');
        $lastRun = Cache::get('portal_sync_last_run');
        if (! $force && $lastRun && now()->diffInSeconds(Carbon::parse($lastRun)) < ($intervalMinutes * 60)) {
            return response()->json([
                'ok' => true,
                'skipped' => true,
                'message' => 'Sincronizacion reciente, se mantiene el intervalo configurado.',
                'pending' => PortalSyncJob::query()->where('status', 'pending')->count(),
                'failed_total' => PortalSyncJob::query()->where('status', 'failed')->count(),
                'interval_minutes' => $intervalMinutes,
            ]);
        }

        Cache::put('portal_sync_last_run', now()->toDateTimeString(), now()->addMinutes($intervalMinutes));

        $jobs = PortalSyncJob::query()
            ->whereIn('status', ['pending', 'failed'])
            ->where(function (Builder $query): void {
                $query->whereNull('available_at')->orWhere('available_at', '<=', now());
            })
            ->oldest('updated_at')
            ->limit(50)
            ->get();

        $sent = 0;
        $failed = 0;

        foreach ($jobs as $job) {
            try {
                $payload = $job->payload;
                $response = Http::timeout(3)
                    ->withHeaders(['X-Portal-Token' => (string) env('PORTAL_SYNC_TOKEN', 'cambia-este-token')])
                    ->post($url, $payload);

                if (! $response->successful() || ($response->json('ok') === false)) {
                    throw new \RuntimeException($response->body() ?: 'Respuesta invalida del portal.');
                }

                $job->update([
                    'status' => 'synced',
                    'attempts' => $job->attempts + 1,
                    'last_error' => null,
                    'synced_at' => now(),
                ]);
                $sent++;
            } catch (\Throwable $exception) {
                $job->update([
                    'status' => 'failed',
                    'attempts' => $job->attempts + 1,
                    'last_error' => Str::limit($exception->getMessage(), 900),
                    'available_at' => now()->addMinutes($intervalMinutes),
                ]);
                $failed++;
            }
        }

        return response()->json([
            'ok' => true,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => PortalSyncJob::query()->where('status', 'pending')->count(),
            'failed_total' => PortalSyncJob::query()->where('status', 'failed')->count(),
            'interval_minutes' => $intervalMinutes,
        ]);
    }

    private function scopedTickets(): Builder
    {
        $user = auth()->user();

        return ParkingTicket::query()
            ->when(! $user->isAdmin(), fn(Builder $query) => $query->where('site_id', $user->site_id));
    }

    private function scopedPayments(): Builder
    {
        $user = auth()->user();

        return Payment::query()->whereHas('ticket', function (Builder $query) use ($user) {
            if (! $user->isAdmin()) {
                $query->where('site_id', $user->site_id);
            }
        });
    }

    private function ticketLookupQuery(string $lookup): Builder
    {
        return $this->scopedTickets()->where(function (Builder $query) use ($lookup) {
            $query->where('ticket_code', $lookup)
                ->orWhere('barcode', $lookup)
                ->orWhere('plate', $lookup);
        })->latest('entry_time');
    }

    private function pendingPaymentsData(): Collection
    {
        return $this->scopedPayments()->with('ticket')->where('status', 'pending')->latest('updated_at')->take(10)->get();
    }

    private function syncPortalTicket(?ParkingTicket $ticket, string $event, ?array $summary = null): void
    {
        if (! $ticket) {
            return;
        }

        $url = (string) env('PORTAL_SYNC_URL', 'https://ingedev94.com/portalricardo/sync.php');
        if ($url === '') {
            return;
        }

        $ticket->loadMissing(['site', 'tariffProfile', 'payment']);
        $payment = $ticket->payment;

        $payload = [
            'token' => (string) env('PORTAL_SYNC_TOKEN', 'cambia-este-token'),
            'event_type' => $event,
            'event_time' => now()->toDateTimeString(),
            'ticket' => [
                'source_ticket_id' => (string) $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'barcode' => $ticket->barcode,
                'plate' => $ticket->plate,
                'vehicle_type' => $ticket->vehicle_type,
                'status' => $ticket->status,
                'location_number' => $ticket->location_number,
                'site_name' => $ticket->site?->name ?? 'Principal',
                'tariff_name' => $ticket->tariffProfile?->name ?? 'Sin tarifa',
                'tariff_type' => $ticket->tariffProfile?->tariff_type ?? 'normal',
                'entry_time' => optional($ticket->entry_time)->toDateTimeString(),
                'exit_time' => optional($ticket->exit_time)->toDateTimeString(),
                'payment_method' => $payment?->method,
                'payment_status' => $payment?->status,
                'paid_at' => optional($payment?->paid_at)->toDateTimeString(),
                'subtotal' => (int) ($payment?->subtotal ?? $summary['subtotal'] ?? 0),
                'discount' => (int) ($payment?->discount ?? $summary['discount'] ?? 0),
                'surcharge' => (int) ($payment?->surcharge ?? $summary['surcharge'] ?? 0),
                'tax' => (int) ($payment?->tax ?? $summary['tax'] ?? 0),
                'total' => (int) ($payment?->total ?? $summary['total'] ?? 0),
                'duration_minutes' => (int) ($summary['minutes'] ?? ($ticket->entry_time ? $ticket->entry_time->diffInMinutes($ticket->exit_time ?? now()) : 0)),
                'synced_at' => now()->toDateTimeString(),
            ],
        ];

        try {
            PortalSyncJob::updateOrCreate(
                ['ticket_code' => $ticket->ticket_code],
                [
                    'parking_ticket_id' => $ticket->id,
                    'event_type' => $event,
                    'payload' => $payload,
                    'status' => 'pending',
                    'last_error' => null,
                    'available_at' => now(),
                    'synced_at' => null,
                ]
            );
        } catch (\Throwable) {
            // La operacion local no depende del portal ni de la cola de sincronizacion.
        }
    }

    private function portalSyncIntervalMinutes(): int
    {
        return max((int) env('PORTAL_SYNC_INTERVAL_MINUTES', 5), 1);
    }

    private function portalSyncOverview(): array
    {
        $intervalMinutes = $this->portalSyncIntervalMinutes();
        $lastRun = Cache::get('portal_sync_last_run');
        $lastSyncedAt = PortalSyncJob::query()->where('status', 'synced')->max('synced_at');
        $pendingCount = PortalSyncJob::query()->where('status', 'pending')->count();
        $failedCount = PortalSyncJob::query()->where('status', 'failed')->count();
        $lastFailure = PortalSyncJob::query()
            ->where('status', 'failed')
            ->whereNotNull('last_error')
            ->latest('updated_at')
            ->value('last_error');

        $base = $lastRun ? Carbon::parse($lastRun) : ($lastSyncedAt ? Carbon::parse($lastSyncedAt) : null);
        $nextRunAt = $base ? $base->copy()->addMinutes($intervalMinutes) : now();

        return [
            'intervalMinutes' => $intervalMinutes,
            'lastRunAt' => $lastRun ? Carbon::parse($lastRun) : null,
            'lastSyncedAt' => $lastSyncedAt ? Carbon::parse($lastSyncedAt) : null,
            'nextRunAt' => $nextRunAt,
            'pendingCount' => $pendingCount,
            'failedCount' => $failedCount,
            'lastFailure' => $lastFailure,
            'isDue' => $nextRunAt->isPast(),
        ];
    }

    private function averageStayMinutes(Builder $tickets): string
    {
        $collection = $tickets->where('status', 'active')->get();
        if ($collection->isEmpty()) {
            return '0 min';
        }

        $average = (int) round($collection->avg(fn(ParkingTicket $ticket) => max($ticket->entry_time?->diffInMinutes(now()) ?? 0, 0)));
        $hours = intdiv($average, 60);
        $minutes = $average % 60;

        return $hours > 0 ? $hours . 'h ' . $minutes . 'm' : $minutes . ' min';
    }

    public function calculateTicket(ParkingTicket $ticket): array
    {
        $tariff = $ticket->tariffProfile;

        $entry = $ticket->entry_time ?? now();
        $exit = $ticket->exit_time ?? now();

        $minutes = max($entry->diffInMinutes($exit), 1);

        $graceMinutes = (int) (
            ($tariff?->grace_entry_minutes ?? 0) +
            ($tariff?->grace_exit_minutes ?? 0)
        );

        $billableMinutes = max($minutes - $graceMinutes, 0);

        $unitRate = (int) ($tariff?->unit_rate ?? 0);

        $type = $tariff?->tariff_type ?? 'normal';

        $fullBlocks = 0;
        $remainderMinutes = 0;
        $fractionUnits = 0;

        $pricingLabel = 'Sin cobro';
        $appliedTariff = $tariff?->name ?? 'Sin tarifa';

        /**
         * 🟢 SIN COBRO
         */
        if ($billableMinutes === 0) {
            $subtotal = 0;
        }

        /**
         * 🔵 CONVENIO
         */
        elseif ($type === 'convenio') {
            $maxMinutes = max((int) ($tariff->max_minutes ?? 0), 1);
            $fullBlocks = (int) ceil($billableMinutes / $maxMinutes);
            $subtotal = $fullBlocks * $unitRate;
            $pricingLabel = $fullBlocks . ' convenio(s), cada uno cubre ' . $maxMinutes . ' min';
        }

        /**
         * 🟣 PLENA CON UMBRAL
         */
        elseif ($type === 'plena') {

            $threshold = max((int) ($tariff->threshold_minutes ?? 0), 0);
            $fullRate = (int) ($tariff->full_rate ?? $unitRate);

            if ($billableMinutes <= $threshold) {
                $normalRate = TariffProfile::query()
                    ->where('vehicle_type', $tariff->vehicle_type)
                    ->where('tariff_type', 'normal')
                    ->where('active', true)
                    ->value('unit_rate');

                $unitRate = (int) ($normalRate ?? $unitRate);
                $fractionUnits = $billableMinutes;
                $subtotal = $billableMinutes * $unitRate;

                $pricingLabel = $billableMinutes . ' minuto(s) antes de tarifa plena';
            } else {
                $fullBlocks = 1;
                $subtotal = $fullRate;

                $pricingLabel = 'Tarifa plena desde ' . $threshold . ' min';
            }
        }

        /**
         * 🟡 NORMAL
         */
        else {
            $fullRateRule = $tariff
                ? TariffProfile::query()
                    ->where('vehicle_type', $tariff->vehicle_type)
                    ->where('tariff_type', 'plena')
                    ->where('active', true)
                    ->first()
                : null;

            $threshold = max((int) ($fullRateRule?->threshold_minutes ?? 0), 0);

            if ($fullRateRule && $billableMinutes > $threshold) {
                $fullBlocks = 1;
                $subtotal = (int) ($fullRateRule->full_rate ?? $fullRateRule->unit_rate ?? 0);
                $appliedTariff = $fullRateRule->name;
                $pricingLabel = 'Tarifa plena desde ' . $threshold . ' min';
            } else {
                $fractionUnits = $billableMinutes;
                $subtotal = $billableMinutes * $unitRate;
                $pricingLabel = $billableMinutes . ' minuto(s) x ' . $this->money($unitRate);
            }
        }

        /**
         * 🧱 TOPE DIARIO
         */
        /**
         * 📌 RECARGOS
         */
        $parkingSubtotal = $subtotal;
        $lockerFee = $ticket->uses_locker ? (int) ($ticket->locker_fee ?: ($ticket->site?->locker_fee ?? 0)) : 0;
        $subtotal += $lockerFee;

        $surcharge = $ticket->is_lost_ticket
            ? (int) ($tariff?->lost_ticket_fee ?? 0)
            : 0;

        $discount = 0;

        $tax = (int) round(
            ($subtotal + $surcharge - $discount) *
                ((float) ($tariff?->tax_percentage ?? 0) / 100)
        );

        $total = max($subtotal + $surcharge - $discount + $tax, 0);

        return [
            'minutes' => $minutes,
            'grace_minutes' => $graceMinutes,
            'billable_minutes' => $billableMinutes,

            'pricing_label' => $pricingLabel,
            'applied_tariff' => $appliedTariff,

            'full_blocks' => $fullBlocks,
            'remainder_minutes' => $remainderMinutes,
            'fraction_units' => $fractionUnits,

            'interval_minutes' => 1,

            'parking_subtotal' => $parkingSubtotal,
            'uses_locker' => (bool) $ticket->uses_locker,
            'locker_number' => $ticket->locker_number,
            'locker_fee' => $lockerFee,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'surcharge' => $surcharge,
            'tax' => $tax,
            'total' => $total,
        ];
    }
    public function deleteTariff(TariffProfile $tariff): RedirectResponse
    {
        $this->authorizeAdmin();

        $tariff->delete();

        return redirect()->route('settings')
            ->with('modal', [
                'type' => 'success',
                'title' => 'Tarifa eliminada',
                'message' => 'La tarifa fue eliminada correctamente.',
            ]);
    }
    private function validateTariff(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['required', 'in:moto,auto,bicicleta'],
            'type' => ['required', 'in:normal,plena,convenio'],
            'threshold_minutes' => ['required_if:type,plena', 'nullable', 'integer', 'min:1'],
            'max_minutes' => ['required_if:type,convenio', 'nullable', 'integer', 'min:1'],
            'full_rate' => ['required_if:type,plena', 'nullable', 'integer', 'min:0'],
            'charge_unit' => ['nullable', 'in:minute'],
            'charge_interval' => ['nullable', 'integer', 'min:1'],
            'unit_rate' => ['required_unless:type,plena', 'nullable', 'integer', 'min:0'],
            'is_full_rate' => ['nullable', 'boolean'],
            'is_agreement' => ['nullable', 'boolean'],
            'agreement_hours' => ['nullable', 'integer', 'min:1'],
            'daily_cap' => ['nullable', 'integer', 'min:0'],
            'grace_entry_minutes' => ['nullable', 'integer', 'min:0'],
            'grace_exit_minutes' => ['nullable', 'integer', 'min:0'],
            'lost_ticket_fee' => ['nullable', 'integer', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);
    }

    private function mapTariffPayload(array $data): array
    {
        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'vehicle_type' => $data['vehicle_type'],
            'tariff_type' => $data['type'],
            'pricing_strategy' => $data['type'] === 'normal' ? 'minute' : 'fixed',
            'billing_mode' => match ($data['type']) {
                'plena' => 'Tarifa plena',
                'convenio' => 'Convenio por tiempo',
                default => 'Cobro por minuto',
            },

            'charge_unit' => 'minute',
            'charge_interval' => 1,
            'unit_rate' => (int) ($data['unit_rate'] ?? 0),

            'threshold_minutes' => $data['type'] === 'plena'
                ? (int) ($data['threshold_minutes'] ?? 0)
                : null,

            'full_rate' => $data['type'] === 'plena'
                ? (int) ($data['full_rate'] ?? 0)
                : null,

            'max_minutes' => $data['type'] === 'convenio'
                ? (int) ($data['max_minutes'] ?? 0)
                : null,

            'is_full_rate' => $data['type'] === 'plena',
            'is_agreement' => $data['type'] === 'convenio',
            'agreement_hours' => $data['type'] === 'convenio'
                ? (int) ceil(((int) ($data['max_minutes'] ?? 0)) / 60)
                : null,

            'daily_cap' => (int) ($data['daily_cap'] ?? 0),
            'grace_entry_minutes' => (int) ($data['grace_entry_minutes'] ?? 0),
            'grace_exit_minutes' => (int) ($data['grace_exit_minutes'] ?? 0),
            'lost_ticket_fee' => (int) ($data['lost_ticket_fee'] ?? 0),
            'tax_percentage' => (float) ($data['tax_percentage'] ?? 0),
            'active' => (bool) ($data['active'] ?? false),
        ];
    }

    private function nextTicketCode(): string
    {
        $date = now()->format('ymd');
        $prefix = 'PK-' . $date . '-';
        $lastCode = ParkingTicket::query()
            ->where('ticket_code', 'like', $prefix . '%')
            ->orderByDesc('ticket_code')
            ->value('ticket_code');

        $sequence = $lastCode
            ? ((int) Str::afterLast($lastCode, '-')) + 1
            : 1;

        do {
            $ticketCode = $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (ParkingTicket::query()->where('ticket_code', $ticketCode)->exists());

        return $ticketCode;
    }

    private function formatReceiptLocation(ParkingTicket $ticket): string
    {
        $siteName = $ticket->site?->name;
        $location = $ticket->location_number ? 'ESPACIO ' . $ticket->location_number : 'SIN UBICACION';

        return Str::upper(trim(($siteName ? $siteName . ' - ' : '') . $location));
    }

    private function formatReceiptDuration(int $minutes): string
    {
        $minutes = max($minutes, 0);
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . ' ' . ($hours === 1 ? 'HORA' : 'HORAS');
        }

        if ($remainingMinutes > 0 || $hours === 0) {
            $parts[] = $remainingMinutes . ' ' . ($remainingMinutes === 1 ? 'MINUTO' : 'MINUTOS');
        }

        return implode(' ', $parts) . ' (' . $minutes . ' MIN)';
    }

    private function sendReceiptToDefaultPrinter(ParkingTicket $ticket, string $type): bool
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return false;
        }

        $ticket->loadMissing(['site', 'payment', 'tariffProfile']);
        $summary = $this->calculateTicket($ticket);
        $rawPath = $this->temporaryPrintPath($ticket, $type, 'bin');
        $scriptPath = $this->temporaryPrintPath($ticket, $type, 'ps1');

        try {
            file_put_contents($rawPath, $this->thermalReceiptBytes($ticket, $type, $summary));

            $script = implode(PHP_EOL, [
            '$ErrorActionPreference = "Stop"',
            '$printerName = ' . $this->powerShellString((string) env('PRINT_PRINTER_NAME', '')),
            'if (-not $printerName) {',
            '    $defaultPrinter = Get-CimInstance -ClassName Win32_Printer | Where-Object { $_.Default -eq $true } | Select-Object -First 1',
            '    if ($defaultPrinter) { $printerName = $defaultPrinter.Name }',
            '}',
            'if (-not $printerName) { throw "No default printer configured. Set PRINT_PRINTER_NAME in .env." }',
            'if ($printerName -match "PDF|XPS|OneNote") { throw "Printer requires driver rendering, not RAW." }',
            '$rawPath = ' . $this->powerShellString($rawPath),
            '$bytes = [System.IO.File]::ReadAllBytes($rawPath)',
            '$source = @"',
            'using System;',
            'using System.Runtime.InteropServices;',
            'public class RawPrinterHelper {',
            '    [StructLayout(LayoutKind.Sequential, CharSet=CharSet.Ansi)]',
            '    public class DOCINFOA {',
            '        [MarshalAs(UnmanagedType.LPStr)] public string pDocName;',
            '        [MarshalAs(UnmanagedType.LPStr)] public string pOutputFile;',
            '        [MarshalAs(UnmanagedType.LPStr)] public string pDataType;',
            '    }',
            '    [DllImport("winspool.Drv", EntryPoint="OpenPrinterA", SetLastError=true, CharSet=CharSet.Ansi, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]',
            '    public static extern bool OpenPrinter(string szPrinter, out IntPtr hPrinter, IntPtr pd);',
            '    [DllImport("winspool.Drv", EntryPoint="ClosePrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]',
            '    public static extern bool ClosePrinter(IntPtr hPrinter);',
            '    [DllImport("winspool.Drv", EntryPoint="StartDocPrinterA", SetLastError=true, CharSet=CharSet.Ansi, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]',
            '    public static extern bool StartDocPrinter(IntPtr hPrinter, int level, [In, MarshalAs(UnmanagedType.LPStruct)] DOCINFOA di);',
            '    [DllImport("winspool.Drv", EntryPoint="EndDocPrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]',
            '    public static extern bool EndDocPrinter(IntPtr hPrinter);',
            '    [DllImport("winspool.Drv", EntryPoint="StartPagePrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]',
            '    public static extern bool StartPagePrinter(IntPtr hPrinter);',
            '    [DllImport("winspool.Drv", EntryPoint="EndPagePrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]',
            '    public static extern bool EndPagePrinter(IntPtr hPrinter);',
            '    [DllImport("winspool.Drv", EntryPoint="WritePrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]',
            '    public static extern bool WritePrinter(IntPtr hPrinter, IntPtr pBytes, int dwCount, out int dwWritten);',
            '    public static bool SendBytesToPrinter(string printerName, byte[] bytes) {',
            '        IntPtr printer;',
            '        DOCINFOA doc = new DOCINFOA();',
            '        doc.pDocName = "ParkManager receipt";',
            '        doc.pDataType = "RAW";',
            '        if (!OpenPrinter(printerName, out printer, IntPtr.Zero)) return false;',
            '        IntPtr unmanagedBytes = Marshal.AllocCoTaskMem(bytes.Length);',
            '        Marshal.Copy(bytes, 0, unmanagedBytes, bytes.Length);',
            '        int written;',
            '        bool success = StartDocPrinter(printer, 1, doc) && StartPagePrinter(printer) && WritePrinter(printer, unmanagedBytes, bytes.Length, out written);',
            '        EndPagePrinter(printer);',
            '        EndDocPrinter(printer);',
            '        ClosePrinter(printer);',
            '        Marshal.FreeCoTaskMem(unmanagedBytes);',
            '        return success && written == bytes.Length;',
            '    }',
            '}',
            '"@',
            'Add-Type -TypeDefinition $source',
            'if (-not [RawPrinterHelper]::SendBytesToPrinter($printerName, $bytes)) { throw "Raw print failed for printer: $printerName" }',
            ]);
            file_put_contents($scriptPath, $script);

            if ($this->runPowerShellScript($scriptPath)) {
                return true;
            }

            if ($this->sendDriverReceiptToDefaultPrinter($ticket, $type, $summary)) {
                return true;
            }

            return $this->sendPlainTextReceiptToDefaultPrinter($ticket, $type);
        } finally {
            $this->deleteTemporaryPrintFiles([$rawPath, $scriptPath]);
        }
    }

    private function sendDriverReceiptToDefaultPrinter(
        ParkingTicket $ticket,
        string $type,
        array $summary
    ): bool {
        $scriptPath = $this->temporaryPrintPath($ticket, $type, 'ps1');
        $barcodePatterns = json_encode($this->code39Patterns(), JSON_UNESCAPED_SLASHES);
        $script = implode(PHP_EOL, [
            '$ErrorActionPreference = "Stop"',
            'Add-Type -AssemblyName System.Drawing',
            '$printerName = ' . $this->powerShellString((string) env('PRINT_PRINTER_NAME', '')),
            'if (-not $printerName) {',
            '    $defaultPrinter = Get-CimInstance -ClassName Win32_Printer | Where-Object { $_.Default -eq $true } | Select-Object -First 1',
            '    if ($defaultPrinter) { $printerName = $defaultPrinter.Name }',
            '}',
            'if (-not $printerName) { throw "No default printer configured. Set PRINT_PRINTER_NAME in .env." }',
            'if ($printerName -match "PDF|XPS|OneNote") { throw "Virtual document printer detected." }',
            '$doc = New-Object System.Drawing.Printing.PrintDocument',
            '$doc.PrinterSettings.PrinterName = $printerName',
            '$doc.DefaultPageSettings.PaperSize = New-Object System.Drawing.Printing.PaperSize("Receipt80", 315, 1100)',
            '$doc.DefaultPageSettings.Margins = New-Object System.Drawing.Printing.Margins(0, 0, 0, 0)',
            '$businessPhone = ' . $this->powerShellString((string) config('app.parking_phone', env('PARKING_PHONE', '3151'))),
            '$businessAddress = ' . $this->powerShellString((string) config('app.parking_address', env('PARKING_ADDRESS', 'Calle 57 dsaj'))),
            '$receiptType = ' . $this->powerShellString($type),
            '$location = ' . $this->powerShellString($this->formatReceiptLocation($ticket)),
            '$plate = ' . $this->powerShellString($ticket->plate),
            '$entryDate = ' . $this->powerShellString((string) optional($ticket->entry_time)->format('d/m/Y h:i A')),
            '$exitDate = ' . $this->powerShellString((string) optional($ticket->exit_time)->format('d/m/Y h:i A')),
            '$duration = ' . $this->powerShellString($this->formatReceiptDuration((int) $summary['minutes'])),
            '$tariff = ' . $this->powerShellString(strtoupper($type === 'salida' ? $summary['applied_tariff'] : ($ticket->tariffProfile?->name ?? 'SIN TARIFA'))),
            '$pricing = ' . $this->powerShellString(strtoupper($summary['pricing_label'] ?? '')),
            '$total = ' . $this->powerShellString($this->money((int) $summary['total'])),
            '$ticketCode = ' . $this->powerShellString($ticket->ticket_code),
            '$patternsJson = ' . $this->powerShellString((string) $barcodePatterns),
            '$patterns = ConvertFrom-Json $patternsJson',
            '$doc.add_PrintPage({',
            '    param($sender, $e)',
            '    $g = $e.Graphics',
            '    $g.PageUnit = [System.Drawing.GraphicsUnit]::Pixel',
            '    $black = [System.Drawing.Brushes]::Black',
            '    $pen = New-Object System.Drawing.Pen([System.Drawing.Color]::Black, 2)',
            '    $font = New-Object System.Drawing.Font("Arial", 11)',
            '    $fontBold = New-Object System.Drawing.Font("Arial", 12, [System.Drawing.FontStyle]::Bold)',
            '    $fontBig = New-Object System.Drawing.Font("Arial", 20, [System.Drawing.FontStyle]::Bold)',
            '    $fontTitle = New-Object System.Drawing.Font("Arial", 18, [System.Drawing.FontStyle]::Bold)',
            '    $x = 14; $w = 287; $script:y = 12',
            '    function CenterText($text, $fontUse) {',
            '        $size = $g.MeasureString($text, $fontUse)',
            '        $g.DrawString($text, $fontUse, $black, [float](($w - $size.Width) / 2 + $x), [float]$script:y)',
            '        $script:y += [int]$size.Height + 2',
            '    }',
            '    function LeftText($text, $fontUse) {',
            '        $g.DrawString($text, $fontUse, $black, [float]$x, [float]$script:y)',
            '        $script:y += [int]$fontUse.GetHeight($g) + 3',
            '    }',
            '    function Rule() {',
            '        $g.DrawLine($pen, $x, $script:y, $x + $w, $script:y)',
            '        $script:y += 9',
            '    }',
            '    function Barcode($value) {',
            '        $encoded = "*" + $value.ToUpper() + "*"',
            '        $narrow = 1; $wide = 3; $height = 58',
            '        $total = 0',
            '        foreach ($ch in $encoded.ToCharArray()) { $patternForTotal = $patterns.PSObject.Properties[[string]$ch].Value; foreach ($p in $patternForTotal.ToCharArray()) { $total += $(if ($p -eq "w") { $wide } else { $narrow }) }; $total += $narrow }',
            '        $bx = [int]($x + (($w - $total) / 2))',
            '        foreach ($ch in $encoded.ToCharArray()) {',
            '            $pattern = $patterns.PSObject.Properties[[string]$ch].Value',
            '            for ($i = 0; $i -lt $pattern.Length; $i++) {',
            '                $bw = if ($pattern[$i] -eq "w") { $wide } else { $narrow }',
            '                if (($i % 2) -eq 0) { $g.FillRectangle($black, $bx, $script:y, $bw, $height) }',
            '                $bx += $bw',
            '            }',
            '            $bx += $narrow',
            '        }',
            '        $script:y += $height + 3',
            '        CenterText $value $font',
            '    }',
            '    CenterText "PARQUEADERO" $fontTitle',
            '    CenterText "DONDE RICHARD" $fontTitle',
            '    CenterText ("Tel: " + $businessPhone) $font',
            '    CenterText ("Direccion: " + $businessAddress) $font',
            '    Rule',
            '    CenterText $(if ($receiptType -eq "ingreso") { "RECIBO DE INGRESO" } else { "RECIBO DE SALIDA" }) $fontTitle',
            '    Rule',
            '    LeftText "UBICACION VEHICULO:" $fontBold',
            '    LeftText $location $font',
            '    Rule',
            '    LeftText "PLACA:" $fontBold',
            '    LeftText $plate $fontBig',
            '    Rule',
            '    LeftText "FECHA ENTRADA:" $fontBold',
            '    LeftText $entryDate $font',
            '    if ($receiptType -eq "salida") {',
            '        LeftText "FECHA SALIDA:" $fontBold',
            '        LeftText $exitDate $font',
            '        Rule',
            '        LeftText "TIEMPO TOTAL:" $fontBold',
            '        LeftText $duration $font',
            '        LeftText "TARIFA:" $fontBold',
            '        LeftText ($tariff + " - " + $pricing) $font',
            '        Rule',
            '        CenterText "VALOR TOTAL:" $fontBold',
            '        CenterText $total $fontBig',
            '        Rule',
            '        CenterText "GRACIAS POR SU VISITA!" $fontBold',
            '    } else {',
            '        LeftText "TARIFA:" $fontBold',
            '        LeftText $tariff $font',
            '        Rule',
            '        CenterText "TICKET N:" $fontBold',
            '        CenterText $ticketCode $fontBig',
            '        Barcode $ticketCode',
            '        Rule',
            '        LeftText "IMPORTANTE" $fontBold',
            '        LeftText "- Conserve este recibo." $font',
            '        LeftText "- Presente este ticket para salida." $font',
            '    }',
            '    $e.HasMorePages = $false',
            '})',
            '$doc.Print()',
        ]);

        try {
            file_put_contents($scriptPath, $script);

            return $this->runPowerShellScript($scriptPath);
        } finally {
            $this->deleteTemporaryPrintFiles([$scriptPath]);
        }
    }

    private function sendPlainTextReceiptToDefaultPrinter(ParkingTicket $ticket, string $type): bool
    {
        $ticket->loadMissing(['site', 'payment', 'tariffProfile']);
        $summary = $this->calculateTicket($ticket);
        $textPath = $this->temporaryPrintPath($ticket, $type, 'txt');
        $scriptPath = $this->temporaryPrintPath($ticket, $type, 'ps1');

        try {
            file_put_contents($textPath, $this->thermalReceiptText($ticket, $type, $summary));

            $script = implode(PHP_EOL, [
                '$ErrorActionPreference = "SilentlyContinue"',
                'Start-Process -FilePath "notepad.exe" -ArgumentList @("/p", ' . $this->powerShellString($textPath) . ') -WindowStyle Hidden -Wait',
            ]);
            file_put_contents($scriptPath, $script);

            return $this->runPowerShellScript($scriptPath);
        } finally {
            $this->deleteTemporaryPrintFiles([$textPath, $scriptPath]);
        }
    }

    private function temporaryPrintPath(ParkingTicket $ticket, string $type, string $extension): string
    {
        $safeCode = preg_replace('/[^A-Z0-9_-]/i', '-', $ticket->ticket_code) ?: 'ticket';
        $prefix = 'parqueadero-' . $type . '-' . $safeCode . '-';
        $path = tempnam(sys_get_temp_dir(), Str::limit($prefix, 60, ''));

        if ($path === false) {
            throw new \RuntimeException('No se pudo crear archivo temporal de impresion.');
        }

        $target = $path . '.' . ltrim($extension, '.');
        rename($path, $target);

        return $target;
    }

    private function deleteTemporaryPrintFiles(array $paths): void
    {
        foreach (array_unique($paths) as $path) {
            if (is_string($path) && is_file($path)) {
                @unlink($path);
            }
        }
    }

    private function thermalReceiptText(ParkingTicket $ticket, string $type, array $summary): string
    {
        $line = str_repeat('-', 32);
        $title = $type === 'ingreso' ? 'RECIBO DE INGRESO' : 'RECIBO DE SALIDA';
        $rows = [
            $line,
            'PARQUEADERO DONDE RICHARD',
            'Tel: ' . config('app.parking_phone', env('PARKING_PHONE', '3151')),
            'Direccion: ' . config('app.parking_address', env('PARKING_ADDRESS', 'Calle 57 dsaj')),
            $line,
            $title,
            $line,
            'UBICACION VEHICULO:',
            $this->formatReceiptLocation($ticket),
            '',
            'PLACA: ' . $ticket->plate,
            'ENTRADA: ' . optional($ticket->entry_time)->format('d/m/Y h:i A'),
        ];

        if ($type === 'salida') {
            $rows = array_merge($rows, [
                'SALIDA:  ' . optional($ticket->exit_time)->format('d/m/Y h:i A'),
                'TIEMPO:  ' . $this->formatReceiptDuration((int) $summary['minutes']),
                'TARIFA:  ' . strtoupper($summary['applied_tariff']),
                $line,
                'VALOR TOTAL: ' . $this->money((int) $summary['total']),
            ]);
        } else {
            $rows = array_merge($rows, [
                'TARIFA: ' . strtoupper($ticket->tariffProfile?->name ?? 'SIN TARIFA'),
                $line,
                'TICKET N: ' . $ticket->ticket_code,
                'CODIGO:   ' . $ticket->ticket_code,
            ]);
        }

        $rows = array_merge($rows, [
            $line,
            $type === 'ingreso' ? 'CONSERVE ESTE RECIBO.' : 'GRACIAS POR SU VISITA!',
            $line,
            '',
            '',
            '',
        ]);

        return implode(PHP_EOL, $rows);
    }

    private function createReceiptPdf(
        ParkingTicket $ticket,
        string $type,
        string $directory,
        string $safeCode,
        string $timestamp,
        array $summary
    ): bool {
        $pdfPath = $directory . DIRECTORY_SEPARATOR . 'recibo-' . $type . '-' . $safeCode . '-' . $timestamp . '.pdf';
        $width = 226.77;  // 80mm en puntos PDF.
        $height = $type === 'ingreso' ? 620.0 : 560.0;
        $commands = [];
        $y = $height - 24;

        $text = function (float $x, float $currentY, string $value, int $size = 10, bool $bold = false) use (&$commands): void {
            $font = $bold ? 'F2' : 'F1';
            $commands[] = 'BT /' . $font . ' ' . $size . ' Tf ' . $x . ' ' . $currentY . ' Td (' . $this->pdfEscape($this->thermalTextLine(trim($value))) . ') Tj ET';
        };

        $center = function (float $currentY, string $value, int $size = 10, bool $bold = false) use (&$commands, $width): void {
            $font = $bold ? 'F2' : 'F1';
            $approxWidth = strlen($value) * $size * 0.52;
            $x = max(($width - $approxWidth) / 2, 8);
            $commands[] = 'BT /' . $font . ' ' . $size . ' Tf ' . $x . ' ' . $currentY . ' Td (' . $this->pdfEscape($this->thermalTextLine(trim($value))) . ') Tj ET';
        };

        $rule = function (float $currentY) use (&$commands, $width): void {
            $commands[] = '12 ' . $currentY . ' m ' . ($width - 12) . ' ' . $currentY . ' l S';
        };

        $center($y, 'PARQUEADERO', 20, true);
        $y -= 22;
        $center($y, 'DONDE RICHARD', 20, true);
        $y -= 18;
        $center($y, 'Tel: ' . config('app.parking_phone', env('PARKING_PHONE', '3151')), 10);
        $y -= 14;
        $center($y, 'Direccion: ' . config('app.parking_address', env('PARKING_ADDRESS', 'Calle 57 dsaj')), 10);
        $y -= 14;
        $rule($y);
        $y -= 24;
        $center($y, $type === 'ingreso' ? 'RECIBO DE INGRESO' : 'RECIBO DE SALIDA', 18, true);
        $y -= 22;
        $rule($y);
        $y -= 18;

        $text(16, $y, 'UBICACION VEHICULO:', 12, true);
        $y -= 14;
        $text(16, $y, $this->formatReceiptLocation($ticket), 11);
        $y -= 16;
        $rule($y);
        $y -= 18;

        $text(16, $y, 'PLACA:', 12, true);
        $y -= 24;
        $text(16, $y, $ticket->plate, 24, true);
        $y -= 16;
        $rule($y);
        $y -= 18;

        $text(16, $y, 'FECHA ENTRADA:', 12, true);
        $y -= 14;
        $text(16, $y, (string) optional($ticket->entry_time)->format('d/m/Y h:i A'), 11);
        $y -= 18;

        if ($type === 'salida') {
            $text(16, $y, 'FECHA SALIDA:', 12, true);
            $y -= 14;
            $text(16, $y, (string) optional($ticket->exit_time)->format('d/m/Y h:i A'), 11);
            $y -= 18;
            $text(16, $y, 'TIEMPO TOTAL:', 12, true);
            $y -= 14;
            $text(16, $y, $this->formatReceiptDuration((int) $summary['minutes']), 11);
            $y -= 18;
            $text(16, $y, 'TARIFA:', 12, true);
            $y -= 14;
            $text(16, $y, strtoupper($summary['applied_tariff']), 10);
            $y -= 14;
            $text(16, $y, strtoupper($summary['pricing_label']), 10);
            $y -= 16;
            $rule($y);
            $y -= 28;
            $center($y, 'VALOR TOTAL:', 15, true);
            $y -= 30;
            $center($y, $this->money((int) $summary['total']), 30, true);
            $y -= 24;
            $rule($y);
            $y -= 24;
            $center($y, 'GRACIAS POR SU VISITA!', 14, true);
        } else {
            $text(16, $y, 'TARIFA:', 12, true);
            $y -= 14;
            $text(16, $y, strtoupper($ticket->tariffProfile?->name ?? 'SIN TARIFA'), 11);
            $y -= 18;
            $rule($y);
            $y -= 22;
            $center($y, 'TICKET N:', 13, true);
            $y -= 30;
            $center($y, $ticket->ticket_code, 26, true);
            $y -= 20;
            $this->appendPdfCode39($commands, $ticket->ticket_code, 24, $y - 58, $width - 48, 52);
            $y -= 76;
            $center($y, $ticket->ticket_code, 11);
            $y -= 16;
            $rule($y);
            $y -= 20;
            $text(16, $y, 'IMPORTANTE', 12, true);
            $y -= 14;
            $text(16, $y, '- Conserve este recibo.', 10);
            $y -= 12;
            $text(16, $y, '- Presente este ticket para salida.', 10);
        }

        $content = implode("\n", $commands) . "\n";
        file_put_contents($pdfPath, $this->buildSimplePdf($content, $width, $height));

        return is_file($pdfPath) && filesize($pdfPath) > 0;
    }

    private function appendPdfCode39(array &$commands, string $value, float $x, float $y, float $maxWidth, float $height): void
    {
        $patterns = $this->code39Patterns();
        $encoded = '*' . $this->sanitizeCode39($value) . '*';
        $narrow = 1.1;
        $wide = 3.0;
        $totalWidth = 0.0;

        foreach (str_split($encoded) as $character) {
            $pattern = $patterns[$character] ?? $patterns['-'];
            foreach (str_split($pattern) as $widthCode) {
                $totalWidth += $widthCode === 'w' ? $wide : $narrow;
            }
            $totalWidth += $narrow;
        }

        $scale = min($maxWidth / max($totalWidth, 1), 1.0);
        $cursor = $x + max(($maxWidth - ($totalWidth * $scale)) / 2, 0);

        foreach (str_split($encoded) as $character) {
            $pattern = $patterns[$character] ?? $patterns['-'];
            foreach (str_split($pattern) as $index => $widthCode) {
                $barWidth = ($widthCode === 'w' ? $wide : $narrow) * $scale;
                if ($index % 2 === 0) {
                    $commands[] = sprintf('%.2F %.2F %.2F %.2F re f', $cursor, $y, $barWidth, $height);
                }
                $cursor += $barWidth;
            }
            $cursor += $narrow * $scale;
        }
    }

    private function buildSimplePdf(string $content, float $width, float $height): string
    {
        $objects = [
            '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj',
            '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj',
            '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $width . ' ' . $height . '] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >> endobj',
            '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj',
            '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >> endobj',
            '6 0 obj << /Length ' . strlen($content) . ' >> stream' . "\n" . $content . 'endstream endobj',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($index = 1; $index <= count($objects); $index++) {
            $pdf .= str_pad((string) $offsets[$index], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private function pdfEscape(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^\x20-\x7E]/', '', $value) ?? '';

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }

    private function thermalReceiptBytes(ParkingTicket $ticket, string $type, array $summary): string
    {
        $line = str_repeat('-', 32);
        $content = '';

        $content .= "\x1B@";              // Inicializar impresora.
        $content .= "\x1B\x74\x10";       // Tabla de caracteres compatible con acentos en muchas ESC/POS.
        $content .= "\x1B\x61\x01";       // Centrado.
        $content .= "\x1B\x21\x30";       // Doble alto/ancho.
        $content .= $this->thermalTextLine('PARQUEADERO');
        $content .= $this->thermalTextLine('DONDE RICHARD');
        $content .= "\x1B\x21\x00";
        $content .= $this->thermalTextLine('Tel: ' . config('app.parking_phone', env('PARKING_PHONE', '3151')));
        $content .= $this->thermalTextLine('Direccion: ' . config('app.parking_address', env('PARKING_ADDRESS', 'Calle 57 dsaj')));
        $content .= $this->thermalTextLine($line);
        $content .= "\x1B\x21\x20";
        $content .= $this->thermalTextLine($type === 'ingreso' ? 'RECIBO DE INGRESO' : 'RECIBO DE SALIDA');
        $content .= "\x1B\x21\x00";
        $content .= $this->thermalTextLine($line);

        $content .= "\x1B\x61\x00";       // Alineado izquierda.
        $content .= $this->thermalTextLine('UBICACION VEHICULO:');
        $content .= $this->thermalTextLine($this->formatReceiptLocation($ticket));
        $content .= $this->thermalTextLine($line);
        $content .= $this->thermalTextLine('PLACA:');
        $content .= "\x1B\x21\x30";
        $content .= $this->thermalTextLine($ticket->plate);
        $content .= "\x1B\x21\x00";
        $content .= $this->thermalTextLine($line);
        $content .= $this->thermalTextLine('FECHA ENTRADA:');
        $content .= $this->thermalTextLine(optional($ticket->entry_time)->format('d/m/Y h:i A'));

        if ($type === 'salida') {
            $content .= $this->thermalTextLine('FECHA SALIDA:');
            $content .= $this->thermalTextLine(optional($ticket->exit_time)->format('d/m/Y h:i A'));
            $content .= $this->thermalTextLine($line);
            $content .= $this->thermalTextLine('TIEMPO TOTAL:');
            $content .= $this->thermalTextLine($this->formatReceiptDuration((int) $summary['minutes']));
            $content .= $this->thermalTextLine('TARIFA:');
            $content .= $this->thermalTextLine(strtoupper($summary['applied_tariff']));
            $content .= $this->thermalTextLine(strtoupper($summary['pricing_label']));
            $content .= $this->thermalTextLine($line);
            $content .= "\x1B\x61\x01";
            $content .= $this->thermalTextLine('VALOR TOTAL:');
            $content .= "\x1B\x21\x30";
            $content .= $this->thermalTextLine($this->money((int) $summary['total']));
            $content .= "\x1B\x21\x00";
            $content .= $this->thermalTextLine($line);
            $content .= $this->thermalTextLine('GRACIAS POR SU VISITA!');
        } else {
            $content .= $this->thermalTextLine('TARIFA:');
            $content .= $this->thermalTextLine(strtoupper($ticket->tariffProfile?->name ?? 'SIN TARIFA'));
            $content .= $this->thermalTextLine($line);
            $content .= "\x1B\x61\x01";
            $content .= $this->thermalTextLine('TICKET N:');
            $content .= "\x1B\x21\x30";
            $content .= $this->thermalTextLine($ticket->ticket_code);
            $content .= "\x1B\x21\x00";
            $content .= "\x1D\x48\x02";   // HRI debajo del codigo.
            $content .= "\x1D\x68\x50";   // Altura del codigo de barras.
            $content .= "\x1D\x77\x02";   // Ancho del codigo de barras.
            $content .= "\x1D\x6B\x04" . $this->sanitizeCode39($ticket->ticket_code) . "\x00";
            $content .= $this->thermalTextLine('');
            $content .= $this->thermalTextLine($line);
            $content .= "\x1B\x61\x00";
            $content .= $this->thermalTextLine('IMPORTANTE');
            $content .= $this->thermalTextLine('- Conserve este recibo.');
            $content .= $this->thermalTextLine('- Presente este ticket para salida.');
            $content .= $this->thermalTextLine('- No se responde por objetos.');
        }

        $content .= "\x1B\x61\x01";
        $content .= $this->thermalTextLine($line);
        $content .= "\n\n\n";
        $content .= "\x1D\x56\x42\x00";   // Corte parcial.

        return $content;
    }

    private function thermalTextLine(?string $value): string
    {
        $value = (string) $value;
        $value = strtr($value, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
        ]);

        return $value . "\n";
    }

    private function sanitizeCode39(string $value): string
    {
        $value = Str::upper($value);
        $value = preg_replace('/[^A-Z0-9 .\-\/+$%]/', '-', $value);

        return $value ?: 'SIN-CODIGO';
    }

    private function runPowerShellScript(string $scriptPath): bool
    {
        $command = 'powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File ' . escapeshellarg($scriptPath) . ' 2>&1';
        $process = @popen($command, 'r');
        if (! $process) {
            return false;
        }

        stream_get_contents($process);
        $exitCode = pclose($process);

        return $exitCode === 0;
    }

    private function powerShellString(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    private function code39Patterns(): array
    {
        return [
            '0' => 'nnnwwnwnn', '1' => 'wnnwnnnnw', '2' => 'nnwwnnnnw', '3' => 'wnwwnnnnn',
            '4' => 'nnnwwnnnw', '5' => 'wnnwwnnnn', '6' => 'nnwwwnnnn', '7' => 'nnnwnnwnw',
            '8' => 'wnnwnnwnn', '9' => 'nnwwnnwnn', 'A' => 'wnnnnwnnw', 'B' => 'nnwnnwnnw',
            'C' => 'wnwnnwnnn', 'D' => 'nnnnwwnnw', 'E' => 'wnnnwwnnn', 'F' => 'nnwnwwnnn',
            'G' => 'nnnnnwwnw', 'H' => 'wnnnnwwnn', 'I' => 'nnwnnwwnn', 'J' => 'nnnnwwwnn',
            'K' => 'wnnnnnnww', 'L' => 'nnwnnnnww', 'M' => 'wnwnnnnwn', 'N' => 'nnnnwnnww',
            'O' => 'wnnnwnnwn', 'P' => 'nnwnwnnwn', 'Q' => 'nnnnnnwww', 'R' => 'wnnnnnwwn',
            'S' => 'nnwnnnwwn', 'T' => 'nnnnwnwwn', 'U' => 'wwnnnnnnw', 'V' => 'nwwnnnnnw',
            'W' => 'wwwnnnnnn', 'X' => 'nwnnwnnnw', 'Y' => 'wwnnwnnnn', 'Z' => 'nwwnwnnnn',
            '-' => 'nwnnnnwnw', '.' => 'wwnnnnwnn', ' ' => 'nwwnnnwnn', '$' => 'nwnwnwnnn',
            '/' => 'nwnwnnnwn', '+' => 'nwnnnwnwn', '%' => 'nnnwnwnwn', '*' => 'nwnnwnwnn',
        ];
    }

    private function code39BarcodeSvg(string $value): string
    {
        $patterns = [
            '0' => 'nnnwwnwnn', '1' => 'wnnwnnnnw', '2' => 'nnwwnnnnw', '3' => 'wnwwnnnnn',
            '4' => 'nnnwwnnnw', '5' => 'wnnwwnnnn', '6' => 'nnwwwnnnn', '7' => 'nnnwnnwnw',
            '8' => 'wnnwnnwnn', '9' => 'nnwwnnwnn', 'A' => 'wnnnnwnnw', 'B' => 'nnwnnwnnw',
            'C' => 'wnwnnwnnn', 'D' => 'nnnnwwnnw', 'E' => 'wnnnwwnnn', 'F' => 'nnwnwwnnn',
            'G' => 'nnnnnwwnw', 'H' => 'wnnnnwwnn', 'I' => 'nnwnnwwnn', 'J' => 'nnnnwwwnn',
            'K' => 'wnnnnnnww', 'L' => 'nnwnnnnww', 'M' => 'wnwnnnnwn', 'N' => 'nnnnwnnww',
            'O' => 'wnnnwnnwn', 'P' => 'nnwnwnnwn', 'Q' => 'nnnnnnwww', 'R' => 'wnnnnnwwn',
            'S' => 'nnwnnnwwn', 'T' => 'nnnnwnwwn', 'U' => 'wwnnnnnnw', 'V' => 'nwwnnnnnw',
            'W' => 'wwwnnnnnn', 'X' => 'nwnnwnnnw', 'Y' => 'wwnnwnnnn', 'Z' => 'nwwnwnnnn',
            '-' => 'nwnnnnwnw', '.' => 'wwnnnnwnn', ' ' => 'nwwnnnwnn', '$' => 'nwnwnwnnn',
            '/' => 'nwnwnnnwn', '+' => 'nwnnnwnwn', '%' => 'nnnwnwnwn', '*' => 'nwnnwnwnn',
        ];

        $encoded = '*' . Str::upper($value) . '*';
        $narrow = 2;
        $wide = 5;
        $height = 78;
        $quiet = 12;
        $x = $quiet;
        $bars = [];

        foreach (str_split($encoded) as $character) {
            $pattern = $patterns[$character] ?? $patterns['-'];

            foreach (str_split($pattern) as $index => $widthCode) {
                $width = $widthCode === 'w' ? $wide : $narrow;
                if ($index % 2 === 0) {
                    $bars[] = '<rect x="' . $x . '" y="0" width="' . $width . '" height="' . $height . '"/>';
                }
                $x += $width;
            }

            $x += $narrow;
        }

        $width = $x + $quiet;

        return '<svg class="receipt-barcode-svg" viewBox="0 0 ' . $width . ' ' . $height . '" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Codigo de barras ' . e($value) . '" preserveAspectRatio="none"><g fill="#000">' . implode('', $bars) . '</g></svg>';
    }

    private function canAccessTicket(ParkingTicket $ticket): bool
    {
        $user = auth()->user();

        return $user->isAdmin() || $ticket->site_id === $user->site_id;
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    private function sharedData(array $data = []): array
    {
        $user = auth()->user();
        $site = $user?->site;

        return array_merge([
            'appName' => config('app.name'),
            'parkName' => 'Parqueadero Donde Richard',
            'currentUser' => $user,
            'operator' => [
                'name' => $user?->name ?? 'Operario',
                'role' => ucfirst($user?->role ?? 'operario'),
                'shift' => $user?->shift_name ?: 'Sin turno',
                'site' => $site?->name ?? 'Principal',
            ],
            'portalSyncInterval' => $this->portalSyncIntervalMinutes(),
        ], $data);
    }

    private function logAction(string $action, string $module, string $detail, mixed $auditable = null, array $meta = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'detail' => $detail,
            'auditable_type' => $auditable ? $auditable::class : null,
            'auditable_id' => $auditable?->id,
            'meta' => $meta,
            'logged_at' => now(),
        ]);
    }

    private function money(int $value): string
    {
        return '$' . number_format($value, 0, ',', '.');
    }
}
