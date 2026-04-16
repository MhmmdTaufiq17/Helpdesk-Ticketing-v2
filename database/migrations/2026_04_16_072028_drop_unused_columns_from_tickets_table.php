<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'last_active_admin_id',
                'admin_last_active_at',
                'last_admin_read_at',
                'ai_summary'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('last_active_admin_id')->nullable();
            $table->timestamp('admin_last_active_at')->nullable();
            $table->timestamp('last_admin_read_at')->nullable();
            $table->text('ai_summary')->nullable()->after('description');
        });
    }
};
