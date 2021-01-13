<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id')->unsigned()->nullable()->index();
            $table->bigInteger('temp_id')->unsigned()->nullable()->index();
            $table->double('amount');
            $table->double('tax_percent');
            $table->double('tax_amount');
            $table->double('net_amount');
            $table->string('payment_id', 80)->unique()->nullable()->default(null);
            $table->string('order_id', 80)->unique();
            $table->string('receipt_id', 80)->unique();
            $table->boolean('paid')->default(0);
            $table->boolean('pending')->default(0);
            $table->boolean('failure')->default(0);
            $table->boolean('fake')->default(0);
            $table->boolean('closed')->default(0);
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
        Schema::dropIfExists('payment_gateways');
    }
}
