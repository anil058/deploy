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
            $table->bigInteger('turnover_id')->nullable();
            $table->string('income_type')->index();
            $table->foreignId('ref_member_id')->nullable()->references('member_id')->on('members')->onDelete('cascade');
            $table->float('level_percent')->nullable();
            $table->float('club_percent')->nullable();
            $table->float('actual_percent')->nullable();
            $table->float('direct_l1_percent')->nullable();
            $table->float('direct_l2_percent')->nullable();
            $table->float('cto')->nullable();
            $table->float('stto')->nullable();
            $table->decimal('ref_amount');
            $table->float('commission')->nullable();
            $table->float('amount');
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
