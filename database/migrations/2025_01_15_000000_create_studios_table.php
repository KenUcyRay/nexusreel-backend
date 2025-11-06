<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['Regular', 'Premium', 'IMAX', '4DX']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('rows')->min(1)->max(20);
            $table->integer('columns')->min(1)->max(20);
            $table->integer('total_seats');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};