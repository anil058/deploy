<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_deposits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id')->index();
            $table->bigInteger('gateway_id')->index();
            $table->decimal('amount');
            $table->decimal('tax_percent');
            $table->decimal('tax_amount');
            $table->decimal('net_amount');
            $table->string('deposit_type')->index(); //FEE/RECHARGE
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
        Schema::dropIfExists('member_deposits');
    }
}
