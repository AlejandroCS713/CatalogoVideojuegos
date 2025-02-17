<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('foro_videojuego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foro_id')->constrained('foros')->onDelete('cascade');
            $table->foreignId('videojuego_id')->constrained('videojuegos')->onDelete('cascade');
            $table->date('fecha_asociacion')->default(now());
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('foro_videojuego');
    }
};
