<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('temp_id')->references('id')->on('temp_members');
            $table->bigInteger('member_id')->unsigned();
            $table->bigInteger('parent_id')->unsigned();
            $table->string('unique_id')->unique()->index();
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('address')->index();
            $table->string('email')->unique();
            $table->string('referal_code')->unique();
            $table->string('father')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('mobile_no');
            $table->foreignId('designation_id')->references('id')->on('designations')->onDelete('cascade');
            $table->integer('current_level')->index();
            $table->integer('recharge_points')->nullable();
            $table->dateTime('joining_date')->nullable()->index();
            $table->string('pin')->nullable();
            $table->text('image')->nullable();
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
        Schema::dropIfExists('members');
    }
}
