<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWebsiteAndAddressToLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'contact')) {
                $table->dropColumn('contact');
            }
            if (Schema::hasColumn('leads', 'title_company')) {
                $table->dropColumn('title_company');
            }
            $table->string('contact')->nullable();
            $table->string('title_company')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'contact')) {
                $table->dropColumn('contact');
            }
            if (Schema::hasColumn('leads', 'title_company')) {
                $table->dropColumn('title_company');
            }
            $table->dropColumn('address');
            $table->dropColumn('website');
        });
    }
}
