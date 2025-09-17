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
        Schema::create('tbl_usermodule', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('opt1')->default(0);
            $table->integer('opt2')->default(0);
            $table->integer('opt3')->default(0);
            $table->integer('code')->default(0);
            $table->string('route', 50)->default('javascript:void()');
            $table->string('module', 50)->nullable();
            $table->string('module_name', 50);
            $table->string('flag', 1)->nullable();
            $table->string('outletcode', 10)->default('');
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();

            $table->primary(['opt1', 'opt2', 'opt3', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_usermodule');
    }
};
