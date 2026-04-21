<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            // ✅ LANGSUNG PAKAI ENUM YANG SUDAH DIUPDATE
            $table->enum('sender_type', ['admin', 'user'])->default('user');
            $table->text('message');
            $table->timestamps();

            $table->index(['ticket_id']);
            $table->index(['user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_replies');
    }
};
