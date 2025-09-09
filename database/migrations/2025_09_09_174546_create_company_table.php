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
        Schema::create('company', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->integer('propertyid');
            $table->string('comp_code', 10);
            $table->string('sn_num', 10)->nullable();
            $table->string('role', 100)->nullable();
            $table->string('comp_name', 100);
            $table->date('start_dt')->nullable();
            $table->date('end_dt')->nullable();
            $table->string('address1', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state_code', 10)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('password');
            $table->string('acname', 100)->default('');
            $table->string('acnum', 100)->default('');
            $table->string('ifsccode', 100)->default('');
            $table->string('bankname', 100)->default('');
            $table->string('branchname', 100)->default('');
            $table->string('cfyear', 9)->nullable();
            $table->string('pfyear', 9)->nullable();
            $table->string('pin', 10)->nullable();
            $table->string('u_name', 100)->nullable();
            $table->dateTime('u_entdt')->nullable();
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 5)->default('a');
            $table->string('pan_no', 20)->nullable();
            $table->string('nationality', 20);
            $table->string('gstin', 20)->nullable();
            $table->string('division_code', 20)->nullable();
            $table->string('legal_name', 100)->nullable();
            $table->string('trade_name', 100)->nullable();
            $table->string('logo', 100)->nullable();
            $table->string('website', 50);
            $table->unsignedInteger('status')->default(1);
            $table->string('remember_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company');
    }
};
