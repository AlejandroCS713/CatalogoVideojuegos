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
        Schema::create('videojuego_plataforma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('videojuego_id')->constrained('videojuegos')->onDelete('cascade');
            $table->foreignId('plataforma_id')->constrained('plataformas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videojuego_plataforma');
    }
};
