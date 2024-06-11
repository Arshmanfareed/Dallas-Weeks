<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_actions', function (Blueprint $table) {
            $table->id();
            $table->string('current_element_id');
            $table->string('next_true_element_id');
            $table->string('next_false_element_id');
            $table->string('status');
            $table->date('end_time');
            $table->string('lead_id');
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
        Schema::dropIfExists('lead_actions');
    }
}
