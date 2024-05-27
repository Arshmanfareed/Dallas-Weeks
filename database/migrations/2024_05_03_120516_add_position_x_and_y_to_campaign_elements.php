<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionXAndYToCampaignElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_elements', function (Blueprint $table) {
            $table->string('position_x');
            $table->string('position_y');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_elements', function (Blueprint $table) {
            $table->dropColumn('position_x');
            $table->dropColumn('position_y');
        });
    }
}
