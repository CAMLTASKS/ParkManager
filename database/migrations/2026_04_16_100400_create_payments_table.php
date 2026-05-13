<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 30);
            $table->unsignedInteger('subtotal')->default(0);
            $table->integer('discount')->default(0);
            $table->unsignedInteger('surcharge')->default(0);
            $table->unsignedInteger('tax')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('received_amount')->default(0);
            $table->unsignedInteger('change_amount')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->string('status', 30)->default('paid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
