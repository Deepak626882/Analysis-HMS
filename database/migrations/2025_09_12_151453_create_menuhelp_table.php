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
        Schema::create('menuhelp', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('compcode', 6)->default('');
            $table->string('username', 15);
            $table->integer('opt1')->default(0);
            $table->integer('opt2')->default(0);
            $table->integer('opt3')->default(0);
            $table->integer('code')->default(0);
            $table->string('route', 50);
            $table->string('module', 50)->default('');
            $table->string('module_name', 50);
            $table->integer('view')->default(0);
            $table->integer('ins')->default(0);
            $table->integer('edit')->default(0);
            $table->string('del', 1)->default('0');
            $table->integer('print')->default(0);
            $table->string('flag', 1)->default('');
            $table->string('outletcode', 10)->default('');
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();

            $table->primary(['propertyid', 'compcode', 'username', 'opt1', 'opt2', 'opt3', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menuhelp');
    }
};
