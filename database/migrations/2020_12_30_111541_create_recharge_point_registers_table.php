<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargePointRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_point_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->references('member_id')->on('members')->onDelete('cascade');
            $table->integer('ref_member_id')->unsigned();
            $table->dateTime('tran_date')->nullable()->index();
            $table->foreignId('payment_id')->nullable()->index();
            $table->foreignId('recharge_id')->nullable()->index();
            $table->integer('recharge_points_added')->nullable();
            $table->integer('recharge_points_consumed')->nullable();
            $table->integer('balance_points')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('recharge_point_registers');
    }
}
