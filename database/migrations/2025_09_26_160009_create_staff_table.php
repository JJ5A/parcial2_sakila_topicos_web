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
        Schema::create('staff', function (Blueprint $table) {
            $table->tinyIncrements('staff_id');
            $table->string('first_name', 45);
            $table->string('last_name', 45);
            $table->unsignedSmallInteger('address_id');
                        $table->binary('picture')->nullable();
            $table->string('email', 50)->nullable();
            $table->unsignedTinyInteger('store_id');
            $table->boolean('active')->default(true);
            $table->string('username', 16);
            $table->string('password', 40)->nullable()->charset('utf8mb4')->collation('utf8mb4_bin');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('store_id', 'idx_fk_store_id');
            $table->index('address_id', 'idx_fk_address_id');
            $table->foreign('store_id', 'fk_staff_store')->references('store_id')->on('store')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('address_id', 'fk_staff_address')->references('address_id')->on('address')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
