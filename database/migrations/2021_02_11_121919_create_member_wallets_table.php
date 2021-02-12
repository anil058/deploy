<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_wallets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id')->unique()->unsigned();
            $table->decimal('welcome_amt');
            $table->decimal('redeemable_amt');
            $table->decimal('non_redeemable');
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
        Schema::dropIfExists('member_wallets');
    }
}
