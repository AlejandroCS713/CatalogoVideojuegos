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
            $table->foreignId('plataforma_id')->nullable()->constrained('plataformas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videojuegos', function (Blueprint $table) {
            $table->dropForeign(['plataforma_id']);
            $table->dropColumn('plataforma_id');
        });
    }
};
