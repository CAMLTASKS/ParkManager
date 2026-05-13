<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('role')->default('operario')->after('email');
            $table->string('shift_name')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('shift_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('site_id');
            $table->dropColumn(['role', 'shift_name', 'is_active']);
        });
    }
};
