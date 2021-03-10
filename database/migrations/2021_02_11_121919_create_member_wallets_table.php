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
            $table->integer('total_members');
            $table->decimal('welcome_amt');
            $table->decimal('redeemable_amt');
            $table->decimal('non_redeemable');
            $table->decimal('level_income');
            $table->decimal('leadership_income');
            $table->decimal('club_income');
            $table->decimal('transferin_amount');
            $table->decimal('transferout_amount');
            $table->timestamps();
        });

        DB::table('member_wallets')->insert(array(
            'member_id' => 0,
            'total_members' => 0,
            'welcome_amt' => 0,
            'redeemable_amt' => 0,
            'non_redeemable' => 0,
            'level_income' => 0,
            'leadership_income' => 0,
            'club_income' => 0,
            'transferin_amount' => 0,
            'transferout_amount' => 0,
        ));

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
