<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\TariffProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DockerSeeder extends Seeder
{
    public function run(): void
    {
        $mainSite = Site::updateOrCreate(
            ['code' => 'PRINCIPAL'],
            [
                'name' => 'Sede Principal',
                'capacity' => 50,
                'locker_fee' => 0,
                'active' => true,
            ]
        );

        $this->createDefaultTariffs();

        if (! User::query()->where('username', 'admin')->exists()) {
            User::create([
                'site_id' => $mainSite->id,
                'username' => 'admin',
                'name' => 'Administrador',
                'email' => 'admin@parqueadero.local',
                'role' => 'admin',
                'shift_name' => 'Principal',
                'is_active' => true,
                'password' => Hash::make(env('DOCKER_ADMIN_PASSWORD', '123Parqueadero.')),
            ]);
        }
    }

    private function createDefaultTariffs(): void
    {
        $tariffs = [
            [
                'name' => 'Moto por minuto',
                'slug' => 'moto-por-minuto',
                'vehicle_type' => 'moto',
                'tariff_type' => 'normal',
                'pricing_strategy' => 'minute',
                'billing_mode' => 'Cobro por minuto',
                'unit_rate' => 100,
                'daily_cap' => 12000,
                'lost_ticket_fee' => 3000,
            ],
            [
                'name' => 'Auto por minuto',
                'slug' => 'auto-por-minuto',
                'vehicle_type' => 'auto',
                'tariff_type' => 'normal',
                'pricing_strategy' => 'minute',
                'billing_mode' => 'Cobro por minuto',
                'unit_rate' => 150,
                'daily_cap' => 25000,
                'lost_ticket_fee' => 5000,
            ],
            [
                'name' => 'Bicicleta por minuto',
                'slug' => 'bicicleta-por-minuto',
                'vehicle_type' => 'bicicleta',
                'tariff_type' => 'normal',
                'pricing_strategy' => 'minute',
                'billing_mode' => 'Cobro por minuto',
                'unit_rate' => 50,
                'daily_cap' => 8000,
                'lost_ticket_fee' => 2000,
            ],
        ];

        foreach ($tariffs as $tariff) {
            TariffProfile::updateOrCreate(
                ['slug' => $tariff['slug']],
                array_merge([
                    'charge_unit' => 'minute',
                    'charge_interval' => 1,
                    'threshold_minutes' => null,
                    'full_rate' => null,
                    'max_minutes' => null,
                    'is_full_rate' => false,
                    'is_agreement' => false,
                    'agreement_hours' => null,
                    'grace_entry_minutes' => 0,
                    'grace_exit_minutes' => 0,
                    'tax_percentage' => 0,
                    'active' => true,
                ], $tariff)
            );
        }
    }
}
