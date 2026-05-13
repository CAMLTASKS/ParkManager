<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table): void {
            if (! Schema::hasColumn('sites', 'locker_fee')) {
                $table->unsignedInteger('locker_fee')->default(0)->after('capacity');
            }
        });

        Schema::table('parking_tickets', function (Blueprint $table): void {
            if (! Schema::hasColumn('parking_tickets', 'uses_locker')) {
                $table->boolean('uses_locker')->default(false)->after('location_number');
            }

            if (! Schema::hasColumn('parking_tickets', 'locker_number')) {
                $table->string('locker_number', 40)->nullable()->after('uses_locker');
            }

            if (! Schema::hasColumn('parking_tickets', 'locker_fee')) {
                $table->unsignedInteger('locker_fee')->default(0)->after('locker_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('parking_tickets', function (Blueprint $table): void {
            if (Schema::hasColumn('parking_tickets', 'locker_fee')) {
                $table->dropColumn('locker_fee');
            }

            if (Schema::hasColumn('parking_tickets', 'locker_number')) {
                $table->dropColumn('locker_number');
            }

            if (Schema::hasColumn('parking_tickets', 'uses_locker')) {
                $table->dropColumn('uses_locker');
            }
        });

        Schema::table('sites', function (Blueprint $table): void {
            if (Schema::hasColumn('sites', 'locker_fee')) {
                $table->dropColumn('locker_fee');
            }
        });
    }
};
