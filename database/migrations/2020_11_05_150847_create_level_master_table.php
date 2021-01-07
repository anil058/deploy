<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLevelMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_masters', function (Blueprint $table) {
            $table->id();
            $table->integer('level');
            $table->integer('required_members');
            $table->float('level_percent');
            $table->string('reward');
        });

        DB::table('level_masters')->insert(array(
            'Level' => 1,
            'required_members' => 5,
            'level_percent' => 20,
            'reward' => ''
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 2,
            'required_members' => 25,
            'level_percent' => 10,
            'reward' => '1000'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 3,
            'required_members' => 125,
            'level_percent' => 7,
            'reward' => '5000'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 4,
            'required_members' => 625,
            'level_percent' => 5,
            'reward' => '25000'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 5,
            'required_members' => 3125,
            'level_percent' => 4,
            'reward' => 'Pulser 150'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 6,
            'required_members' => 15625,
            'level_percent' => 3,
            'reward' => 'Wagon R VXI'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 7,
            'required_members' => 78125,
            'level_percent' => 3,
            'reward' => 'Innova Crysta'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 8,
            'required_members' => 390625,
            'level_percent' => 2.5,
            'reward' => 'Fortuner'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 9,
            'required_members' => 1953125,
            'level_percent' => 2.5,
            'reward' => 'BMW X5'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 10,
            'required_members' => 9765625,
            'level_percent' => 2,
            'reward' => '8000 X FT Land'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 11,
            'required_members' => 48828125,
            'level_percent' => 2,
            'reward' => '38HK Flat + 1 Cr'
        ));
        DB::table('level_masters')->insert(array(
            'Level' => 12,
            'required_members' => 244140625,
            'level_percent' => 2,
            'reward' => 'Bunglow + 2.5Cr'
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('level_masters');
    }
}
