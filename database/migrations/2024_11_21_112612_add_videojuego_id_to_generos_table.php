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
        Schema::table('generos', function (Blueprint $table) {
            $table->foreignId('videojuego_id')->nullable()->constrained('videojuegos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generos', function (Blueprint $table) {
            $table->dropForeign(['videojuego_id']);
            $table->dropColumn('videojuego_id');
        });
    }
};
