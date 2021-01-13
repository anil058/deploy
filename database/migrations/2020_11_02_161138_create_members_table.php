<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->bigInteger('member_id')->unique()->unsigned();
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
            $table->foreignId('designation_id')->references('id')->on('club_masters')->onDelete('cascade');
            $table->integer('current_level')->index();
            $table->integer('recharge_points')->nullable();
            $table->dateTime('joining_date')->nullable()->index();
            $table->string('pin')->nullable();
            $table->text('image')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();
            // $table->enum('user_status', array('Active', 'Deactive'))->default('Active');
        });
        DB::table('members')->insert(array(
            'temp_id' => 0,
            'member_id' => 0,
            'parent_id' => 0,
            'unique_id' => '000000',
            'first_name' => 'Mansha Real Rupees',
            'last_name' => '',
            'address' => '',
            'email' => 'mansha@gmail.com',
            'referal_code' => '0000000000',
            'father' => '',
            'pan_no' => '',
            'mobile_no' => '',
            'designation_id' => 1,
            'current_level' => 0,
            'recharge_points' => 0,
            'joining_date' => date('Y-m-d H:m:s')
        ));

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
