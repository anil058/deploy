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
            $table->integer('int_value')->nullable();
            $table->float('float_value')->nullable();
            $table->boolean('bool_value')->default(0);
            $table->dateTime('date_value')->nullable();
        });

        DB::table('params')->insert(array(
            'param' => 'TXN_COUNTER',
            'string_value' => '',
            'int_value' => 1,
            'bool_value' => false,
            'date_value' => null
        ));
        DB::table('params')->insert(array(
            'param' => 'MEMBERSHIP_FEE',
            'string_value' => '',
            'int_value' => 1000,
            'bool_value' => false
        ));
        DB::table('params')->insert(array(
            'param' => 'MEMBER_COUNTER',
            'string_value' => 'M',
            'int_value' => 1,
            'bool_value' => false
        ));
        // DB::table('params')->insert(array(
        //     'param' => 'BRONZ',
        //     'string_value' => '',
        //     'int_value' => 5,
        //     'bool_value' => false
        // ));
        // DB::table('params')->insert(array(
        //     'param' => 'SILVER',
        //     'string_value' => '',
        //     'int_value' => 125,
        //     'bool_value' => false
        // ));
        // DB::table('params')->insert(array(
        //     'param' => 'GOLD',
        //     'string_value' => '',
        //     'int_value' => 3375,
        //     'bool_value' => false
        // ));
        // DB::table('params')->insert(array(
        //     'param' => 'DIAMOND',
        //     'string_value' => '',
        //     'int_value' => 91125,
        //     'bool_value' => false
        // ));
        // DB::table('params')->insert(array(
        //     'param' => 'ROYALTY',
        //     'string_value' => '',
        //     'int_value' => 729000,
        //     'bool_value' => false
        // ));
        DB::table('params')->insert(array(
            'param' => 'CASHBACK_REWARD',
            'string_value' => '',
            'int_value' => 30,
            'bool_value' => false
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
