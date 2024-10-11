<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_blacklist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('user')->onDelete('cascade');
            $table->unsignedBigInteger('team_id');
            $table->foreign('team_id')->references('id')->on('team')->onDelete('cascade');
            $table->string('keyword');
            $table->string('blacklist_type');
            $table->string('comparison_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_blacklist');
    }
};
