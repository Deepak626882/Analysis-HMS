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
        Schema::create('updatelog', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->string('mainmenu')->nullable();
            $table->string('submenu')->nullable();
            $table->string('pagename')->nullable();
            $table->string('summary')->nullable();
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updatelog');
    }
};
