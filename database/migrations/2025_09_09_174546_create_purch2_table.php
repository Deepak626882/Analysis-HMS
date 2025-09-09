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
        Schema::create('purch2', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 100);
            $table->integer('sno');
            $table->string('vtype', 6);
            $table->integer('vno');
            $table->string('vprefix', 5);
            $table->date('vdate');
            $table->string('partycode', 9);
            $table->string('restcode', 7);
            $table->integer('mrno');
            $table->string('contradocid', 35);
            $table->integer('contrasno');
            $table->string('item', 10);
            $table->decimal('qtyiss', 10);
            $table->decimal('qtyrec', 10);
            $table->string('unit', 6);
            $table->decimal('rate', 10);
            $table->decimal('amount', 10);
            $table->decimal('taxper', 10);
            $table->decimal('taxamt', 10);
            $table->decimal('discper', 10);
            $table->decimal('discamt', 10);
            $table->string('remarks', 35);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
            $table->decimal('total', 10);
            $table->string('discapp', 1);
            $table->decimal('roundoff', 10);
            $table->string('departcode', 9);
            $table->string('godcode', 9);
            $table->decimal('chalqty', 10);
            $table->decimal('recdqty', 10);
            $table->decimal('accqty', 10);
            $table->decimal('rejqty', 10);
            $table->string('recdunit', 7);
            $table->string('specification', 15);
            $table->decimal('itemrate', 10);
            $table->string('delflag', 1);
            $table->decimal('convratio', 10);
            $table->decimal('postval', 10);
            $table->decimal('landval', 10);
            $table->decimal('issqty', 10);
            $table->string('issuunit');
            $table->string('taxstru');
            $table->string('accode');

            $table->primary(['propertyid', 'docid', 'sno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purch2');
    }
};
