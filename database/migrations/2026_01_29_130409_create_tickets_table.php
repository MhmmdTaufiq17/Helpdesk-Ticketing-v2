<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 80)->unique();
            $table->string('client_name');
            $table->string('client_email');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamps();

            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['category_id']);
            $table->index(['ticket_code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
