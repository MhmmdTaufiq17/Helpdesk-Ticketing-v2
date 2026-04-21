<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek koneksi database
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE ticket_replies MODIFY sender_type ENUM('admin', 'user') NOT NULL");
        } else {
            // Untuk SQLite (tidak support MODIFY), kita skip
            Schema::table('ticket_replies', function ($table) {
                $table->string('sender_type')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE ticket_replies MODIFY sender_type ENUM('admin') NOT NULL");
        } else {
            Schema::table('ticket_replies', function ($table) {
                $table->string('sender_type')->nullable()->change();
            });
        }
    }
};
