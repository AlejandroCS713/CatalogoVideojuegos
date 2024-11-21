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
        Schema::table('videojuegos', function (Blueprint $table) {
            $table->foreignId('genero_id')->nullable()->constrained('generos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videojuegos', function (Blueprint $table) {
            $table->dropForeign(['genero_id']);
            $table->dropColumn('genero_id');
        });
    }
};
