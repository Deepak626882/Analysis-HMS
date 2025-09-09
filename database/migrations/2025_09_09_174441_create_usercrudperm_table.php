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
        Schema::create('usercrudperm', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->integer('propertyid');
            $table->string('u_name', 100);
            $table->string('role', 100);
            $table->string('menuid', 10);
            $table->string('menuname', 100);
            $table->integer('creates')->nullable()->default(1);
            $table->integer('updates')->nullable()->default(1);
            $table->integer('deletes')->nullable()->default(1);
            $table->integer('retrieve')->nullable()->default(1);
            $table->integer('download')->nullable()->default(1);
            $table->integer('other')->nullable();
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
            $table->longText('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usercrudperm');
    }
};
