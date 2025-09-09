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
        Schema::create('nightauditlog', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->string('propertyid', 50);
            $table->date('ncurdate');
            $table->string('narration', 50)->default('');
            $table->string('u_name', 50)->nullable();
            $table->dateTime('u_entdt')->useCurrent();
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nightauditlog');
    }
};
