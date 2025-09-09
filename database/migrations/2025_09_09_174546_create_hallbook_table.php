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
        Schema::create('hallbook', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid');
            $table->string('vtype', 5);
            $table->integer('vno');
            $table->time('vtime');
            $table->integer('vprefix');
            $table->date('vdate');
            $table->string('partyname', 50);
            $table->string('add1', 50)->default('');
            $table->string('add2', 50)->default('');
            $table->string('city', 8)->nullable()->default('');
            $table->string('panno', 10)->default('');
            $table->string('mobileno', 10)->default('');
            $table->string('mobileno1', 10)->default('');
            $table->string('func_name', 50);
            $table->string('restcode', 7);
            $table->string('housekeeping', 150)->default('');
            $table->string('frontoff', 150)->default('');
            $table->string('engg', 150)->default('');
            $table->string('security', 150)->default('');
            $table->string('chef', 150)->default('');
            $table->string('board', 150)->default('');
            $table->string('menuspl1', 150)->default('');
            $table->string('menuspl2', 150)->default('');
            $table->string('menuspl3', 150)->default('');
            $table->string('menuspl4', 150)->default('');
            $table->string('menuspl5', 150)->default('');
            $table->string('menuspl6', 150)->default('');
            $table->string('menuspl7', 150)->default('');
            $table->integer('expatt');
            $table->integer('guaratt');
            $table->float('coverrate', null, 0)->default(0);
            $table->string('companycode', 8)->default('');
            $table->string('remark', 60)->default('');
            $table->string('bookingagent', 8)->default('');
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);

            $table->primary(['propertyid', 'docid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallbook');
    }
};
