<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tariff_profile_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_code')->unique();
            $table->string('barcode')->unique();
            $table->string('plate', 12)->index();
            $table->string('vehicle_type', 30);
            $table->string('status', 30)->default('active');
            $table->unsignedInteger('location_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_lost_ticket')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_tickets');
    }
};
