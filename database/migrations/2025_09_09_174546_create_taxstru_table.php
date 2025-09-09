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
        Schema::create('taxstru', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('str_code', 100);
            $table->string('name', 100);
            $table->integer('sno')->default(1);
            $table->string('tax_code', 100);
            $table->string('nature', 100)->nullable();
            $table->float('rate', null, 0)->nullable();
            $table->decimal('limits', 10)->nullable();
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
            $table->string('sysYN', 1)->nullable();
            $table->string('condapp', 100)->nullable();
            $table->string('comp_operator', 100)->nullable();
            $table->decimal('limit1', 10)->nullable();

            $table->primary(['propertyid', 'str_code', 'sno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxstru');
    }
};
