<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeatInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_info', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();      
            $table->string('username')->nullable();      
            $table->string('city')->nullable();      
            $table->string('state')->nullable();      
            $table->string('company_name')->nullable();      
            $table->string('email')->nullable();      
            $table->string('phone_number')->nullable();      
            $table->string('profile_summary')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('github')->nullable();
            $table->string('status')->nullable();                 
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
        Schema::dropIfExists('seat_info');
    }
}
