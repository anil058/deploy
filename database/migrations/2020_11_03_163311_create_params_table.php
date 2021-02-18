<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('params', function (Blueprint $table) {
            $table->id();
            $table->string('param')->unique();
            $table->string('string_value');
            $table->text('long_text');
            $table->integer('int_value')->nullable();
            $table->float('float_value')->nullable();
            $table->boolean('bool_value')->default(0);
            $table->dateTime('date_value')->nullable();
        });

        DB::table('params')->insert(array(
            'param' => 'TXN_COUNTER',
            'string_value' => '',
            'long_text' => '',
            'int_value' => 1,
            'bool_value' => false,
            'date_value' => null
        ));
        DB::table('params')->insert(array(
            'param' => 'MEMBERSHIP_FEE',
            'string_value' => '',
            'long_text' => '',
            'int_value' => 1000,
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'MEMBER_COUNTER',
            'string_value' => 'M',
            'long_text' => '',
            'int_value' => 1,
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'RECHARGE_TOKEN',
            'string_value' => '',
            'long_text' => '',
            'int_value' => 0,
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'CASHBACK_REWARD',
            'string_value' => '',
            'long_text' => '',
            'int_value' => 30,
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'TAX_PERCENT',
            'string_value' => '18',
            'long_text' => '',
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'LEVEL1_LEADERSHIP_INCOME',
            'string_value' => '',
            'int_value' => 5,
            'long_text' => '',
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'LEVEL2_LEADERSHIP_INCOME',
            'string_value' => '',
            'int_value' => 3,
            'long_text' => '',
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'MOBILE_WELCOME_ADJUSTMENT_PERCENT',
            'string_value' => '',
            'int_value' => 2,
            'long_text' => '',
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'MOBILE_RECHARGE_LIMIT',
            'string_value' => '',
            'int_value' => 0,
            'long_text' => '',
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'ALLOW_COMPANY_REFERAL_CODE',
            'string_value' => '',
            'long_text' => '',
            'bool_value' => true
        ));
        DB::table('params')->insert(array(
            'param' => 'ALLOW_MOBILE_RECAHRGE',
            'string_value' => '',
            'long_text' => '',
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'ALLOW_NEW_MEMBERS',
            'string_value' => '',
            'long_text' => '',
            'bool_value' => true
        ));
        DB::table('params')->insert(array(
            'param' => 'RECHARGE_CASHBACK_PERCENT',
            'string_value' => '',
            'int_value' => 2,
            'long_text' => '',
            'bool_value' => true
        ));
        DB::table('params')->insert(array(
            'param' => 'TEAM_CASHBACK_PERCENT',
            'string_value' => '',
            'int_value' => 2,
            'long_text' => '',
            'bool_value' => true
        ));

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('params');
    }
}
