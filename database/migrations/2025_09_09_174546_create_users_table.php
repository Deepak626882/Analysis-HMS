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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->unique('id');
            $table->string('name');
            $table->integer('propertyid');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('role', 100);
            $table->integer('superwiser')->default(1);
            $table->integer('backdate')->default(0);
            $table->string('u_name', 50)->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->string('u_ae', 1)->default('a');
            $table->integer('status')->default(1);

            $table->primary(['name', 'propertyid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
