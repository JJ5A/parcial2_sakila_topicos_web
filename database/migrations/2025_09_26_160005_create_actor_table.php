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
        Schema::create('actor', function (Blueprint $table) {
            $table->smallIncrements('actor_id');
            $table->string('first_name', 45);
            $table->string('last_name', 45);
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('last_name', 'idx_actor_last_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actor');
    }
};
