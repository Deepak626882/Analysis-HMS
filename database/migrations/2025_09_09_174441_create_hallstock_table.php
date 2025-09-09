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
        Schema::create('hallstock', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid');
            $table->integer('sno');
            $table->string('vtype', 3);
            $table->integer('vno');
            $table->integer('vprefix');
            $table->date('vdate');
            $table->string('party', 30);
            $table->string('restcode', 7);
            $table->string('item', 10);
            $table->decimal('qtyiss', 10);
            $table->string('unit', 6);
            $table->decimal('rate', 10);
            $table->decimal('amount', 10);
            $table->decimal('taxamt', 10);
            $table->decimal('taxper', 10)->default(0);
            $table->decimal('discper', 10);
            $table->decimal('discamt', 10);
            $table->string('remarks', 25);
            $table->decimal('total', 10);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);

            $table->primary(['propertyid', 'docid', 'sno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallstock');
    }
};
