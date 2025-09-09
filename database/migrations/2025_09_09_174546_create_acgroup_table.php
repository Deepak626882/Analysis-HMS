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
        Schema::create('acgroup', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('group_code', 10);
            $table->string('group_name', 25);
            $table->string('nature', 25);
            $table->string('sys_group', 10);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->char('u_ae', 1)->default('a');

            $table->primary(['propertyid', 'group_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acgroup');
    }
};
