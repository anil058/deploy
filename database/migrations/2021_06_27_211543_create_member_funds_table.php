<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_funds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id')->unique()->unsigned();
            $table->string('razor_fund_id')->unique()->index();
            $table->string('account_type')->index();
            $table->string('bank_name')->index();
            $table->string('ifsc')->index();
            $table->string('account_no')->index();
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
        Schema::dropIfExists('member_funds');
    }
}
