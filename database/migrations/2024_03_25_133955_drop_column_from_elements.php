<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnFromElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('elements', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('seat_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('elements', function (Blueprint $table) {
            $table->string('user_id')->nullable();
            $table->string('seat_id')->nullable();
        });
    }
}
