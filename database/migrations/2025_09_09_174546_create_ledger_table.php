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
        Schema::create('ledger', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 100);
            $table->integer('vsno');
            $table->string('vtype', 5);
            $table->integer('vno');
            $table->string('vprefix', 5);
            $table->date('vdate');
            $table->string('subcode', 50);
            $table->decimal('amtcr', 10);
            $table->decimal('amtdr', 10);
            $table->string('contrasub', 50);
            $table->string('chqno', 15)->nullable();
            $table->date('chqdate')->nullable();
            $table->string('delflag', 1)->default('N');
            $table->date('clgdate')->nullable();
            $table->string('narration', 500)->default('');
            $table->string('groupcode', 8)->default('');
            $table->string('groupnature', 50);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);

            $table->primary(['propertyid', 'docid', 'vsno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger');
    }
};
