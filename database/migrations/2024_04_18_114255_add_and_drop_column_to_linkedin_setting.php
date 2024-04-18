<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAndDropColumnToLinkedinSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('linkedin_setting', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('linkedin_setting', function (Blueprint $table) {
            $table->dropColumn('value');
            $table->string('is_active');
        });
    }
}
