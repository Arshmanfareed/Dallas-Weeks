<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_to_roles', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('role_id');
            $table->tinyInteger('permission_id');
            $table->boolean('access');
            $table->boolean('view_only');
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
        Schema::dropIfExists('permission_to_roles');
    }
}
