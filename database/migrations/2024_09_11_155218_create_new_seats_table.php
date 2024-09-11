<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('seats');
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('company_info_id');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('seat_info_id');
            // $table->unsignedBigInteger('integration_id');
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('company_info_id')->references('id')->on('company_info')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('seat_info_id')->references('id')->on('seat_info')->onDelete('cascade');
            // $table->foreign('integration_id')->references('id')->on('integrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats');
    }
}
