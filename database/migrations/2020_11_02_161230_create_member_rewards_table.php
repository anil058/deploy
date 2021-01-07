<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->integer('level_id')->index();
            $table->integer('member_count')->index();
            $table->dateTime('tran_date')->nullable()->index();
            $table->string('reward_name')->nullable();
            $table->dateTime('qualifying_date')->nullable()->index();
            $table->dateTime('payment_date')->nullable()->index();
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
        Schema::dropIfExists('member_rewards');
    }
}
