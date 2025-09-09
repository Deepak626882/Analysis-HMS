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
        Schema::create('expsheet', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 50);
            $table->string('vtype', 6);
            $table->integer('vno')->default(0);
            $table->string('vprefix', 4);
            $table->date('vdate');
            $table->time('vtime');
            $table->float('dramt', null, 0)->default(0);
            $table->string('drac', 8)->default('');
            $table->float('cramt', null, 0)->default(0);
            $table->string('crac', 8)->default('');
            $table->string('remark', 100)->default('');
            $table->string('delflag', 1)->default('');
            $table->string('u_name', 15)->nullable()->default('');
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->default('');

            $table->index(['vdate', 'propertyid', 'vtype'], 'idx_expsheet');
            $table->primary(['propertyid', 'docid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expsheet');
    }
};
