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
        Schema::table('store', function (Blueprint $table) {
            $table->unique('manager_staff_id', 'idx_unique_manager');
            $table->foreign('manager_staff_id', 'fk_store_staff')->references('staff_id')->on('staff')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('address_id', 'fk_store_address')->references('address_id')->on('address')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store', function (Blueprint $table) {
            $table->dropForeign('fk_store_staff');
            $table->dropForeign('fk_store_address');
            $table->dropUnique('idx_unique_manager');
        });
    }
};
