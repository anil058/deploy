<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreignId('bonus_type_id')->references('id')->on('bonus_types')->onDelete('cascade');
            $table->foreignId('bonus_rule_id')->references('id')->on('bonus_rules')->onDelete('cascade');
            $table->double('amount');
            $table->foreignId('payout_id')->references('id')->on('payouts')->onDelete('cascade');
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
        Schema::dropIfExists('member_incomes');
    }
}
