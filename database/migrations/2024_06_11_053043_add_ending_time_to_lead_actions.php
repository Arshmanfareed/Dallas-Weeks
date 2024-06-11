<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndingTimeToLeadActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_actions', function (Blueprint $table) {
            $table->dropColumn('end_time');
            $table->dateTime('ending_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_actions', function (Blueprint $table) {
            $table->dropColumn('ending_time');
        });
    }
}
