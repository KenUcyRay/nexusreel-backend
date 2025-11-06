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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('image')->nullable();
            $table->integer('duration');
            $table->string('genre');
            $table->string('rating');
            $table->string('director');
            $table->text('production_team')->nullable();
            $table->enum('trailer_type', ['url', 'upload'])->default('url');
            $table->text('trailer_url')->nullable();
            $table->string('trailer_file')->nullable();
            $table->enum('status', ['coming_soon', 'live_now'])->default('coming_soon');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
