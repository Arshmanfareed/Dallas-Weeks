<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewSeatInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('seat_info');
        Schema::create('seat_info', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email');
            $table->string('phone_number');
            $table->text('profile_summary')->nullable();
            $table->string('linkedin');
            $table->string('twitter');
            $table->string('github');
            $table->string('status');
            $table->string('account_id');
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
