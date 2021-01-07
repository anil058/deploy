<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelAchieversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_achievers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->integer('level_id')->index();
            $table->dateTime('tran_date')->nullable()->index();
            $table->dateTime('qualifying_date')->nullable()->index();
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
        Schema::dropIfExists('level_achievers');
    }
}
