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
        Schema::create('rental', function (Blueprint $table) {
            $table->increments('rental_id');
            $table->dateTime('rental_date');
            $table->unsignedMediumInteger('inventory_id');
            $table->unsignedSmallInteger('customer_id');
            $table->dateTime('return_date')->nullable();
            $table->unsignedTinyInteger('staff_id');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->unique(['rental_date', 'inventory_id', 'customer_id']);
            $table->index('inventory_id', 'idx_fk_inventory_id');
            $table->index('customer_id', 'idx_fk_customer_id');
            $table->index('staff_id', 'idx_fk_staff_id');
            
            $table->foreign('staff_id', 'fk_rental_staff')->references('staff_id')->on('staff')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('inventory_id', 'fk_rental_inventory')->references('inventory_id')->on('inventory')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('customer_id', 'fk_rental_customer')->references('customer_id')->on('customer')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental');
    }
};
