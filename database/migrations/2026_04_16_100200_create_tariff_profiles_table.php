<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_profiles', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->enum('vehicle_type', ['bicicleta', 'moto', 'auto']);

            // Nueva lógica
            $table->enum('tariff_type', ['normal', 'plena', 'convenio'])->default('normal');

            // normal: cada X minutos/horas cobra unit_rate
            // plena: se activa después de threshold_minutes y cubre max_minutes
            // convenio: valor fijo hasta max_minutes
            $table->string('charge_unit')->default('minute');
            $table->unsignedInteger('charge_interval')->default(1);
            $table->unsignedInteger('unit_rate')->default(0);

            $table->unsignedInteger('threshold_minutes')->nullable(); // solo plena
            $table->unsignedInteger('full_rate')->nullable();          // valor unico de plena
            $table->unsignedInteger('max_minutes')->nullable();       // plena/convenio

            // Extras
            $table->unsignedInteger('daily_cap')->default(0);
            $table->unsignedInteger('grace_entry_minutes')->default(0);
            $table->unsignedInteger('grace_exit_minutes')->default(0);
            $table->unsignedInteger('lost_ticket_fee')->default(0);
            $table->unsignedDecimal('tax_percentage', 5, 2)->default(0);
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_profiles');
    }
};
