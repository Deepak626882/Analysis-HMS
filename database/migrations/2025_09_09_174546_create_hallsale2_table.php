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
        Schema::create('hallsale2', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid');
            $table->integer('sno');
            $table->integer('sno1');
            $table->string('vtype', 3);
            $table->integer('vno');
            $table->integer('vprefix');
            $table->date('vdate');
            $table->string('restcode', 8);
            $table->string('taxcode', 8);
            $table->decimal('basevalue', 10);
            $table->decimal('taxper', 10);
            $table->decimal('taxamt', 10);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);

            $table->primary(['propertyid', 'docid', 'sno', 'sno1']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallsale2');
    }
};
