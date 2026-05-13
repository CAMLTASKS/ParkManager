<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\ParkingTicket;
use App\Models\Payment;
use App\Models\Site;
use App\Models\TariffProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $mainSite = Site::updateOrCreate(
            ['code' => 'CENTRO'],
            ['name' => 'Sede Principal - Centro', 'capacity' => 50, 'active' => true]
        );

        Site::updateOrCreate(
            ['code' => 'NORTE'],
            ['name' => 'Sede Norte', 'capacity' => 35, 'active' => true]
        );

        $autoProfile = TariffProfile::updateOrCreate(
            ['slug' => 'automovil-estandar'],
            [
                'name' => 'Automovil estandar',
                'vehicle_type' => 'auto',
                'tariff_type' => 'normal',
                'charge_unit' => 'minute',
                'charge_interval' => 1,
                'unit_rate' => 100,
                'threshold_minutes' => null,
                'full_rate' => null,
                'max_minutes' => null,
                'is_full_rate' => false,
                'is_agreement' => false,
                'agreement_hours' => null,
                'daily_cap' => 25000,
                'grace_entry_minutes' => 15,
                'grace_exit_minutes' => 10,
                'lost_ticket_fee' => 5000,
                'tax_percentage' => 0,
                'active' => true,
            ]
        );

        $motoProfile = TariffProfile::updateOrCreate(
            ['slug' => 'motocicleta'],
            [
                'name' => 'Motocicleta',
                'vehicle_type' => 'moto',
                'tariff_type' => 'plena',
                'charge_unit' => 'minute',
                'charge_interval' => 1,
                'unit_rate' => 50,
                'threshold_minutes' => 240,
                'full_rate' => 8000,
                'max_minutes' => null,
                'is_full_rate' => true,
                'is_agreement' => false,
                'agreement_hours' => null,
                'daily_cap' => 12000,
                'grace_entry_minutes' => 10,
                'grace_exit_minutes' => 10,
                'lost_ticket_fee' => 3000,
                'tax_percentage' => 0,
                'active' => true,
            ]
        );

        TariffProfile::updateOrCreate(
            ['slug' => 'gimnasio-2-horas'],
            [
                'name' => 'Convenio Gimnasio',
                'vehicle_type' => 'moto',
                'tariff_type' => 'convenio',
                'charge_unit' => 'minute',
                'charge_interval' => 1,
                'unit_rate' => 5000,
                'threshold_minutes' => null,
                'full_rate' => null,
                'max_minutes' => 720,
                'is_full_rate' => false,
                'is_agreement' => true,
                'agreement_hours' => 12,
                'daily_cap' => 0,
                'grace_entry_minutes' => 0,
                'grace_exit_minutes' => 10,
                'lost_ticket_fee' => 3000,
                'tax_percentage' => 0,
                'active' => true,
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@parkmanager.com'],
            [
                'site_id' => $mainSite->id,
                'username' => 'admin',
                'name' => 'Administrador General',
                'role' => 'admin',
                'shift_name' => 'Administrativo',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        $operator = User::updateOrCreate(
            ['email' => 'operador@sede.com'],
            [
                'site_id' => $mainSite->id,
                'username' => 'operador',
                'name' => 'Carlos Op.',
                'role' => 'operario',
                'shift_name' => 'Manana (06:00 - 14:00)',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        $cashier = User::updateOrCreate(
            ['email' => 'ana@parkmanager.com'],
            [
                'site_id' => $mainSite->id,
                'username' => 'ana.caja',
                'name' => 'Ana Martinez',
                'role' => 'operario',
                'shift_name' => 'Manana (06:00 - 14:00)',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        $supervisor = User::updateOrCreate(
            ['email' => 'roberto@parkmanager.com'],
            [
                'site_id' => $mainSite->id,
                'username' => 'roberto.admin',
                'name' => 'Roberto Gomez',
                'role' => 'admin',
                'shift_name' => 'Tarde (14:00 - 22:00)',
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        $blocked = User::updateOrCreate(
            ['email' => 'luis@parkmanager.com'],
            [
                'site_id' => $mainSite->id,
                'username' => 'luis.op',
                'name' => 'Luis Fernando',
                'role' => 'operario',
                'shift_name' => 'Sin turno',
                'is_active' => false,
                'password' => Hash::make('password'),
            ]
        );

        $activeTickets = [
            [
                'ticket_code' => 'TKT-1001',
                'barcode' => 'TKT-1001',
                'plate' => 'ABC-123',
                'vehicle_type' => 'auto',
                'entry_time' => Carbon::now()->subHours(2)->subMinutes(45),
                'profile_id' => $autoProfile->id,
                'location_number' => 11,
            ],
            [
                'ticket_code' => 'TKT-1002',
                'barcode' => 'TKT-1002',
                'plate' => 'XYZ-987',
                'vehicle_type' => 'moto',
                'entry_time' => Carbon::now()->subHour()->subMinutes(30),
                'profile_id' => $motoProfile->id,
                'location_number' => 5,
            ],
            [
                'ticket_code' => 'TKT-1003',
                'barcode' => 'TKT-1003',
                'plate' => 'DEF-456',
                'vehicle_type' => 'auto',
                'entry_time' => Carbon::now()->subHours(4),
                'profile_id' => $autoProfile->id,
                'location_number' => 9,
            ],
        ];

        foreach ($activeTickets as $ticket) {
            ParkingTicket::updateOrCreate(
                ['ticket_code' => $ticket['ticket_code']],
                [
                    'site_id' => $mainSite->id,
                    'tariff_profile_id' => $ticket['profile_id'],
                    'barcode' => $ticket['barcode'],
                    'plate' => $ticket['plate'],
                    'vehicle_type' => $ticket['vehicle_type'],
                    'status' => 'active',
                    'location_number' => $ticket['location_number'],
                    'customer_name' => null,
                    'customer_phone' => null,
                    'created_by' => $operator->id,
                    'closed_by' => null,
                    'entry_time' => $ticket['entry_time'],
                    'exit_time' => null,
                    'notes' => null,
                    'is_lost_ticket' => false,
                ]
            );
        }

        $paidTickets = [
            [
                'code' => 'TKT-8902',
                'plate' => 'ABC-123',
                'type' => 'auto',
                'entry' => Carbon::today()->setTime(8, 15),
                'exit' => Carbon::today()->setTime(10, 45),
                'subtotal' => 12150,
                'discount' => 1215,
                'surcharge' => 0,
                'tax' => 1565,
                'total' => 12500,
                'method' => 'Efectivo',
                'operator_id' => $cashier->id,
                'status' => 'paid',
            ],
            [
                'code' => 'TKT-8901',
                'plate' => 'XYZ-987',
                'type' => 'moto',
                'entry' => Carbon::today()->setTime(9, 0),
                'exit' => Carbon::today()->setTime(10, 30),
                'subtotal' => 3000,
                'discount' => 0,
                'surcharge' => 0,
                'tax' => 0,
                'total' => 3000,
                'method' => 'Tarjeta',
                'operator_id' => $cashier->id,
                'status' => 'paid',
            ],
            [
                'code' => 'TKT-8900',
                'plate' => 'DEF-456',
                'type' => 'auto',
                'entry' => Carbon::today()->setTime(7, 30),
                'exit' => Carbon::today()->setTime(10, 0),
                'subtotal' => 8500,
                'discount' => 0,
                'surcharge' => 0,
                'tax' => 0,
                'total' => 8500,
                'method' => 'Anulada',
                'operator_id' => $supervisor->id,
                'status' => 'voided',
            ],
        ];

        foreach ($paidTickets as $row) {
            $ticket = ParkingTicket::updateOrCreate(
                ['ticket_code' => $row['code']],
                [
                    'site_id' => $mainSite->id,
                    'tariff_profile_id' => $row['type'] === 'moto' ? $motoProfile->id : $autoProfile->id,
                    'barcode' => $row['code'],
                    'plate' => $row['plate'],
                    'vehicle_type' => $row['type'],
                    'status' => $row['status'],
                    'location_number' => rand(1, 20),
                    'customer_name' => 'Cliente '.$row['plate'],
                    'customer_phone' => '3000000000',
                    'created_by' => $operator->id,
                    'closed_by' => $row['operator_id'],
                    'entry_time' => $row['entry'],
                    'exit_time' => $row['exit'],
                    'notes' => null,
                    'is_lost_ticket' => false,
                ]
            );

            Payment::updateOrCreate(
                ['parking_ticket_id' => $ticket->id],
                [
                    'user_id' => $row['operator_id'],
                    'method' => $row['method'],
                    'subtotal' => $row['subtotal'],
                    'discount' => $row['discount'],
                    'surcharge' => $row['surcharge'],
                    'tax' => $row['tax'],
                    'total' => $row['total'],
                    'received_amount' => $row['method'] === 'Efectivo' ? 15000 : $row['total'],
                    'change_amount' => $row['method'] === 'Efectivo' ? 2500 : 0,
                    'paid_at' => $row['exit'],
                    'status' => $row['status'],
                ]
            );
        }

        AuditLog::updateOrCreate(
            ['action' => 'Cobro', 'detail' => 'Liquidacion Ticket #TKT-8902 ($12,500)'],
            ['user_id' => $cashier->id, 'logged_at' => Carbon::today()->setTime(10, 45)]
        );
        AuditLog::updateOrCreate(
            ['action' => 'Anulacion', 'detail' => 'Anulacion Ticket #TKT-8900 (Error de placa)'],
            ['user_id' => $supervisor->id, 'logged_at' => Carbon::today()->setTime(10, 0)]
        );
        AuditLog::updateOrCreate(
            ['action' => 'Login', 'detail' => 'Inicio de sesion (Caja Salida Principal)'],
            ['user_id' => $operator->id, 'logged_at' => Carbon::today()->setTime(6, 0)]
        );
        AuditLog::updateOrCreate(
            ['action' => 'Bloqueo', 'detail' => 'Usuario bloqueado por 3 intentos fallidos'],
            ['user_id' => $blocked->id, 'logged_at' => Carbon::yesterday()->setTime(18, 30)]
        );
        AuditLog::updateOrCreate(
            ['action' => 'Configuracion', 'detail' => 'Parametros iniciales del sistema creados'],
            ['user_id' => $admin->id, 'logged_at' => Carbon::today()->setTime(5, 50)]
        );
    }
}
