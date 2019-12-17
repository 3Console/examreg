<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('email_verification_code')->nullable();
            $table->unsignedTinyInteger('email_verified')->default(1);
            $table->string('msv')->unique();
            $table->string('username')->nullable();
            $table->string('full_name')->nullable();
            $table->date('dob')->nullable()->comment("date of birth");
            $table->unsignedTinyInteger('sex')->comment('sex of user, 1:male, 0:female]');
            $table->string('course')->nullable();
            $table->string('unit')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
