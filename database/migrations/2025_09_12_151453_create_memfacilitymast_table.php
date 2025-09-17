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
        Schema::create('memfacilitymast', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->integer('code');
            $table->string('name', 35);
            $table->string('sname', 4);
            $table->string('ChargeType', 12);
            $table->decimal('fixedrate', 10, 0);
            $table->string('taxstru', 8);
            $table->string('accode', 8);
            $table->string('activeyn', 1);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->string('u_ae', 1);

            $table->primary(['propertyid', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memfacilitymast');
    }
};
