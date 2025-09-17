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
        Schema::create('companydiscount', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('compcode', 9);
            $table->integer('sno');
            $table->integer('roomcatcode');
            $table->integer('adult');
            $table->decimal('fixrate', 10)->default(0);
            $table->string('plan', 9)->default('');
            $table->decimal('planamount', 10)->default(0);
            $table->string('taxinc', 1)->default('');
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);

            $table->primary(['propertyid', 'compcode', 'roomcatcode', 'adult']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companydiscount');
    }
};
