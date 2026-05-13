<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_sync_jobs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parking_ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ticket_code', 80)->unique();
            $table->string('event_type', 60)->default('sync');
            $table->json('payload');
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('available_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_sync_jobs');
    }
};
