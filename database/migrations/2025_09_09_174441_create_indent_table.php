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
        Schema::create('indent', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 50);
            $table->string('vtype', 6);
            $table->integer('vno');
            $table->integer('vprefix');
            $table->date('vdate');
            $table->time('vtime');
            $table->string('department', 50);
            $table->string('godown', 50);
            $table->string('remarks', 50)->default('');
            $table->string('veryfiuser', 15)->default('');
            $table->date('veryfidate')->nullable();
            $table->string('veryfiremark', 50)->default('');
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
            $table->string('company', 6)->default('');
            $table->string('refdocId', 50)->default('');
            $table->string('clearyn', 1)->default('');

            $table->primary(['propertyid', 'docid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indent');
    }
};
