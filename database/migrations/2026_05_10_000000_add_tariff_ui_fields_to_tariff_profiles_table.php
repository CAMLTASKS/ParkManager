<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tariff_profiles', function (Blueprint $table): void {
            if (! Schema::hasColumn('tariff_profiles', 'pricing_strategy')) {
                $table->string('pricing_strategy')->default('minute');
            }

            if (! Schema::hasColumn('tariff_profiles', 'billing_mode')) {
                $table->string('billing_mode')->default('Cobro por minuto');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tariff_profiles', function (Blueprint $table): void {
            if (Schema::hasColumn('tariff_profiles', 'billing_mode')) {
                $table->dropColumn('billing_mode');
            }

            if (Schema::hasColumn('tariff_profiles', 'pricing_strategy')) {
                $table->dropColumn('pricing_strategy');
            }
        });
    }
};
