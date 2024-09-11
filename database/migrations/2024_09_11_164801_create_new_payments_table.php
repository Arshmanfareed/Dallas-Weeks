<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('physical_payment');
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->decimal('item_price', 10, 2);
            $table->string('item_price_currency');
            $table->string('transaction_id')->unique();
            $table->string('status');
            $table->decimal('paid_amount', 10, 2);
            $table->string('paid_amount_currency');
            $table->string('uploaded_month', 2);
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
        Schema::dropIfExists('payments');
    }
}
