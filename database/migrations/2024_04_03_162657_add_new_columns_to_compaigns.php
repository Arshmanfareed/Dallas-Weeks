<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToCompaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compaigns', function (Blueprint $table) {
            $table->string('compaign_type');
            $table->string('compaign_url');
            $table->string('compaign_connection');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compaigns', function (Blueprint $table) {
            $table->dropColumn('compaign_type');
            $table->dropColumn('compaign_url');
            $table->dropColumn('compaign_connection');
        });
    }
}
