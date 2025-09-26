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
        Schema::create('film', function (Blueprint $table) {
            $table->smallIncrements('film_id');
            $table->string('title', 128);
            $table->text('description')->nullable();
            $table->year('release_year')->nullable();
            $table->unsignedTinyInteger('language_id');
            $table->unsignedTinyInteger('original_language_id')->nullable();
            $table->unsignedTinyInteger('rental_duration')->default(3);
            $table->decimal('rental_rate', 4, 2)->default(4.99);
            $table->unsignedSmallInteger('length')->nullable();
            $table->decimal('replacement_cost', 5, 2)->default(19.99);
            $table->enum('rating', ['G', 'PG', 'PG-13', 'R', 'NC-17'])->default('G');
            $table->set('special_features', ['Trailers', 'Commentaries', 'Deleted Scenes', 'Behind the Scenes'])->nullable();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('title', 'idx_title');
            $table->index('language_id', 'idx_fk_language_id');
            $table->index('original_language_id', 'idx_fk_original_language_id');
            $table->foreign('language_id', 'fk_film_language')->references('language_id')->on('language')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('original_language_id', 'fk_film_language_original')->references('language_id')->on('language')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film');
    }
};
