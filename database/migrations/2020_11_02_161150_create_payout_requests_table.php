<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('member_id')->references('id')->on('members')->onDelete('cascade')->unsigned()->index();
            $table->string('reference_id')->nullable();
            $table->bigInteger('fund_id')->index();
            $table->string('payout_id')->unique()->index();
            $table->double('request_amount')->unique();
            $table->string('status')->nullable(); //Paid,Pending,Rejected
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
        Schema::dropIfExists('payout_requests');
    }
}
