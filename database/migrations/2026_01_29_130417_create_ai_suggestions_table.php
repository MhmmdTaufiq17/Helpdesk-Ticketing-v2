<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained()->onDelete('cascade');
            $table->text('ai_summary')->nullable();
            $table->string('ai_suggested_category')->nullable();
            $table->enum('ai_suggested_priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_suggestions');
    }
};
