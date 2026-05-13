<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tariff_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('tariff_profiles', 'charge_unit')) {
                $table->string('charge_unit')->default('minute');
            }
            if (! Schema::hasColumn('tariff_profiles', 'charge_interval')) {
                $table->unsignedInteger('charge_interval')->default(1);
            }
            if (! Schema::hasColumn('tariff_profiles', 'unit_rate')) {
                $table->unsignedInteger('unit_rate')->default(0);
            }
            if (! Schema::hasColumn('tariff_profiles', 'is_full_rate')) {
                $table->boolean('is_full_rate')->default(false);
            }
            if (! Schema::hasColumn('tariff_profiles', 'is_agreement')) {
                $table->boolean('is_agreement')->default(false);
            }
            if (! Schema::hasColumn('tariff_profiles', 'agreement_hours')) {
                $table->unsignedInteger('agreement_hours')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
