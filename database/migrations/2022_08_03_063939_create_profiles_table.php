<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('usertype');
            $table->string('email')->unique();
            $table->string('fullname')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('gender')->nullable();
            $table->string('gps_long')->nullable();
            $table->string('gps_lat')->nullable();
            $table->string('image_link')->nullable();
            $table->string('id_link')->nullable();
            $table->integer('approval_status')->default(0);
            $table->string('approved_by')->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
