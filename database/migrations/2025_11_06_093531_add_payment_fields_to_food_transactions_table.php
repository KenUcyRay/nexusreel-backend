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
        Schema::table('food_transactions', function (Blueprint $table) {
            $table->string('order_id')->unique()->nullable();
            $table->integer('amount')->nullable();
            $table->json('items')->nullable();
            $table->string('snap_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_transactions', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'amount', 'items', 'snap_token']);
        });
    }
};
