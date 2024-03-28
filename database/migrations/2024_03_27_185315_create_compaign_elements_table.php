<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaignElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compaign_elements', function (Blueprint $table) {
            $table->id();
            $table->string('element_id');
            $table->string('compaign_id');
            $table->string('compaign_element_id');
            $table->string('user_id');
            $table->string('seat_id');
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
        Schema::dropIfExists('compaign_elements');
    }
}
