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
            $table->foreignId('member_id')->references('member_id')->on('members')->onDelete('cascade');
            $table->string('income_type')->index();
            $table->foreignId('ref_member_id')->nullable()->references('member_id')->on('members')->onDelete('cascade');
            $table->foreignId('club_turnover_id')->nullable()->references('id')->on('club_turn_overs')->onDelete('cascade');
            $table->foreignId('nw_turnover_id')->nullable()->references('id')->on('n_w_turn_overs')->onDelete('cascade');
            $table->foreignId('royalty_turnover_id')->nullable()->references('id')->on('royalty_turn_overs')->onDelete('cascade');
            $table->float('level_percent')->nullable();
            $table->float('nw_percent')->nullable();
            $table->float('royalty_percent')->nullable();
            $table->float('direct_l1_percent')->nullable();
            $table->float('direct_l2_percent')->nullable();
            $table->decimal('ref_amount');
            $table->float('commission')->nullable();
            $table->double('amount');
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
