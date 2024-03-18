<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PhysicalPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physical_payment', function (Blueprint $table) {
            $table->id();
            $table->string('physical_payment_name')->nullable();      
            $table->string('physical_payment_email')->nullable();      
            $table->string('physical_payment_num')->nullable();      
            $table->string('product_id')->nullable();      
            $table->string('swap_products_id')->nullable();      
            $table->string('swap_request_user_id')->nullable();      
            $table->string('physical_payment_item_name')->nullable();      
            $table->string('physical_payment_item_number')->nullable();
            $table->string('physical_payment_item_price')->nullable();
            $table->string('physical_payment_item_price_currency')->nullable();
            $table->string('physical_payment_paid_amount')->nullable();
            $table->string('uploaded_month')->nullable();
            $table->string('physical_payment_paid_amount_currency')->nullable();
            $table->string('physical_payment_txn_id')->nullable();
            $table->string('physical_payment_status')->nullable();
            $table->string('physical_payment_created')->nullable();
            $table->string('physical_payment_modified')->nullable();
            $table->string('user_id')->nullable();
            $table->string('swap_request_id')->nullable();            
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
        Schema::dropIfExists('physical_payment');
    }
}
