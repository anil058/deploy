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
            $table->double('request_amount')->unique();
            $table->double('payment_amount')->unique();
            $table->string('status')->default('PENDING')->index();
            $table->bigInteger('approver_id')->nullable();
            $table->dateTime('approved_on')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('account_number')->nullable();
            $table->string('razor_contact_id')->unique()->index();
            $table->string('razor_fund_id')->unique()->index();
            $table->string('payout_id')->unique()->index();
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
