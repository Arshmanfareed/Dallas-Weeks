<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CretaeBlacklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('permission_name');
            $table->string('permission_slug');
            $table->tinyInteger('user_id');
            $table->tinyInteger('team_id');
            $table->boolean('access');
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
        Schema::dropIfExists('global_permissions');
    }
}
