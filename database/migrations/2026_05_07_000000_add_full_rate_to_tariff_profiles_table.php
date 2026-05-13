<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tariff_profiles', 'tariff_type')) {
            Schema::table('tariff_profiles', function (Blueprint $table) {
                $table->string('tariff_type')->default('normal');
            });
        }

        if (! Schema::hasColumn('tariff_profiles', 'charge_unit')) {
            Schema::table('tariff_profiles', function (Blueprint $table) {
                $table->string('charge_unit')->default('minute');
            });
        }

        if (! Schema::hasColumn('tariff_profiles', 'charge_interval')) {
            Schema::table('tariff_profiles', function (Blueprint $table) {
                $table->unsignedInteger('charge_interval')->default(1);
            });
        }

        if (! Schema::hasColumn('tariff_profiles', 'unit_rate')) {
            Schema::table('tariff_profiles', function (Blueprint $table) {
                $table->unsignedInteger('unit_rate')->default(0);
            });
        }

        if (! Schema::hasColumn('tariff_profiles', 'threshold_minutes')) {
            Schema::table('tariff_profiles', function (Blueprint $table) {
                $table->unsignedInteger('threshold_minutes')->nullable();
            });
        }

        if (! Schema::hasColumn('tariff_profiles', 'max_minutes')) {
            Schema::table('tariff_profiles', function (Blueprint $table) {
                $table->unsignedInteger('max_minutes')->nullable();
            });
        }

        if (! Schema::hasColumn('tariff_profiles', 'full_rate')) {
            Schema::table('tariff_profiles', function (Blueprint $table) {
                $table->unsignedInteger('full_rate')->nullable();
            });
        }

        if (Schema::hasColumn('tariff_profiles', 'is_agreement')) {
            DB::table('tariff_profiles')
                ->where('is_agreement', true)
                ->update(['tariff_type' => 'convenio']);
        }

        if (Schema::hasColumn('tariff_profiles', 'is_full_rate')) {
            DB::table('tariff_profiles')
                ->where('is_full_rate', true)
                ->update(['tariff_type' => 'plena']);
        }

        if (Schema::hasColumn('tariff_profiles', 'fraction_rate')) {
            DB::table('tariff_profiles')
                ->where('unit_rate', 0)
                ->whereNotNull('fraction_rate')
                ->update(['unit_rate' => DB::raw('fraction_rate')]);
        }

        if (Schema::hasColumn('tariff_profiles', 'full_rate_threshold_minutes')) {
            DB::table('tariff_profiles')
                ->where('tariff_type', 'plena')
                ->whereNull('threshold_minutes')
                ->whereNotNull('full_rate_threshold_minutes')
                ->update(['threshold_minutes' => DB::raw('full_rate_threshold_minutes')]);
        }

        if (Schema::hasColumn('tariff_profiles', 'agreement_hours')) {
            DB::table('tariff_profiles')
                ->where('tariff_type', 'convenio')
                ->whereNull('max_minutes')
                ->whereNotNull('agreement_hours')
                ->update(['max_minutes' => DB::raw('agreement_hours * 60')]);
        }

        if (Schema::hasColumn('tariff_profiles', 'flat_rate')) {
            DB::table('tariff_profiles')
                ->where('tariff_type', 'plena')
                ->whereNull('full_rate')
                ->whereNotNull('flat_rate')
                ->update(['full_rate' => DB::raw('flat_rate')]);
        }

        DB::table('tariff_profiles')
            ->where('tariff_type', 'plena')
            ->whereNull('full_rate')
            ->update(['full_rate' => DB::raw('unit_rate')]);
    }

    public function down(): void
    {
        Schema::table('tariff_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('tariff_profiles', 'full_rate')) {
                $table->dropColumn('full_rate');
            }
        });
    }
};
