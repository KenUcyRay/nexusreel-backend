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
        Schema::create('movie_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->integer('amount');
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->json('seats');
            $table->integer('ticket_count');
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_transactions');
    }
};
