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
        Schema::create('film_category', function (Blueprint $table) {
            $table->unsignedSmallInteger('film_id');
            $table->unsignedTinyInteger('category_id');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->primary(['film_id', 'category_id']);
            
            $table->foreign('film_id', 'fk_film_category_film')->references('film_id')->on('film')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('category_id', 'fk_film_category_category')->references('category_id')->on('category')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film_category');
    }
};
