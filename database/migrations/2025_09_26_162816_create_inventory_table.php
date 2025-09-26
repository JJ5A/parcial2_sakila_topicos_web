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
        Schema::create('inventory', function (Blueprint $table) {
            $table->mediumIncrements('inventory_id');
            $table->unsignedSmallInteger('film_id');
            $table->unsignedTinyInteger('store_id');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('film_id', 'idx_fk_film_id');
            $table->index(['store_id', 'film_id'], 'idx_store_id_film_id');
            $table->foreign('store_id', 'fk_inventory_store')->references('store_id')->on('store')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('film_id', 'fk_inventory_film')->references('film_id')->on('film')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
