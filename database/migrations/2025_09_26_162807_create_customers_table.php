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
        Schema::create('customer', function (Blueprint $table) {
            $table->smallIncrements('customer_id');
            $table->unsignedTinyInteger('store_id');
            $table->string('first_name', 45);
            $table->string('last_name', 45);
            $table->string('email', 50)->nullable();
            $table->unsignedSmallInteger('address_id');
            $table->boolean('active')->default(true);
            $table->dateTime('create_date');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('store_id', 'idx_fk_store_id');
            $table->index('address_id', 'idx_fk_address_id');
            $table->index('last_name', 'idx_last_name');
            $table->foreign('address_id', 'fk_customer_address')->references('address_id')->on('address')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('store_id', 'fk_customer_store')->references('store_id')->on('store')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
