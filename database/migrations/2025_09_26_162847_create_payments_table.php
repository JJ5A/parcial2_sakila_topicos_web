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
        Schema::create('payment', function (Blueprint $table) {
            $table->smallIncrements('payment_id');
            $table->unsignedSmallInteger('customer_id');
            $table->unsignedTinyInteger('staff_id');
            $table->unsignedInteger('rental_id')->nullable();
            $table->decimal('amount', 5, 2);
            $table->dateTime('payment_date');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('staff_id', 'idx_fk_staff_id');
            $table->index('customer_id', 'idx_fk_customer_id');
            
            $table->foreign('rental_id', 'fk_payment_rental')->references('rental_id')->on('rental')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('customer_id', 'fk_payment_customer')->references('customer_id')->on('customer')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('staff_id', 'fk_payment_staff')->references('staff_id')->on('staff')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
