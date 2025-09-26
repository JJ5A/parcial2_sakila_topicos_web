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
        Schema::create('store', function (Blueprint $table) {
            $table->tinyIncrements('store_id');
            $table->unsignedTinyInteger('manager_staff_id');
            $table->unsignedSmallInteger('address_id');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            // Agregamos los constraints después de crear staff
            $table->index('address_id', 'idx_fk_address_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store');
    }
};
