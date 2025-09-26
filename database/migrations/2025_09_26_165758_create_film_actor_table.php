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
        Schema::create('film_actor', function (Blueprint $table) {
            $table->unsignedSmallInteger('actor_id');
            $table->unsignedSmallInteger('film_id');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->primary(['actor_id', 'film_id']);
            $table->index('film_id', 'idx_fk_film_id');
            
            $table->foreign('actor_id', 'fk_film_actor_actor')->references('actor_id')->on('actor')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('film_id', 'fk_film_actor_film')->references('film_id')->on('film')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film_actor');
    }
};
