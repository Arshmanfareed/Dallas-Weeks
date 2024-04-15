<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTableConditionalElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('conditional_elements');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('conditional_elements', function (Blueprint $table) {
            $table->id();
            $table->string('element_name');
            $table->string('element_slug');
            $table->string('element_icon');
            $table->timestamps();
        });
    }
}
