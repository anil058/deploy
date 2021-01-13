<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id')->index();
            $table->string('mobile_no')->index();
            $table->integer('provider_id')->index();
            $table->string('provider_name')->index();
            $table->integer('amount')->unsigned();
            $table->integer('status_id')->nullable()->unsigned(); //0 or 1 -> success, 2->failure
            $table->string('utr')->nullable()->index();
            $table->string('report_id')->nullable()->index();
            $table->string('orderid')->nullable()->index();
            $table->string('message')->nullable()->index();
            $table->boolean('verified')->default(0);
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
        Schema::dropIfExists('recharge_requests');
    }
}
