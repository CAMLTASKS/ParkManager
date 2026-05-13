<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE tariff_profiles MODIFY tariff_type ENUM('normal', 'plena', 'convenio', 'mensualidad') NOT NULL DEFAULT 'normal'");
        } catch (Throwable) {
            // En motores sin ENUM estricto no es necesario alterar la columna.
        }

        Schema::create('monthly_memberships', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tariff_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('plate', 12);
            $table->string('vehicle_type', 30);
            $table->string('vehicle_brand')->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('starts_at');
            $table->date('next_payment_date');
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();

            $table->unique(['site_id', 'plate']);
            $table->index(['status', 'next_payment_date']);
        });

        Schema::create('monthly_membership_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monthly_membership_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('receipt_code')->unique();
            $table->string('method', 40)->default('efectivo');
            $table->unsignedInteger('amount')->default(0);
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('paid_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['paid_at', 'method']);
        });

        Schema::create('monthly_membership_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monthly_membership_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parking_ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 30);
            $table->string('plate', 12);
            $table->string('ticket_code')->nullable();
            $table->timestamp('occurred_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_membership_activities');
        Schema::dropIfExists('monthly_membership_payments');
        Schema::dropIfExists('monthly_memberships');

        try {
            DB::statement("ALTER TABLE tariff_profiles MODIFY tariff_type ENUM('normal', 'plena', 'convenio') NOT NULL DEFAULT 'normal'");
        } catch (Throwable) {
            //
        }
    }
};
