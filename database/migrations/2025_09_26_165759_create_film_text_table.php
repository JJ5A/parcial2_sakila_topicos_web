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
        Schema::create('film_text', function (Blueprint $table) {
            $table->unsignedSmallInteger('film_id')->primary();
            $table->string('title', 255);
            $table->text('description')->nullable();
            
            // Creamos solo índice en título por limitaciones de MySQL
            $table->index('title', 'idx_title_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film_text');
    }
};
