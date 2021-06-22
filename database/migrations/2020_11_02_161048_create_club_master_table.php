<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateClubMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_masters', function (Blueprint $table) {
            $table->id();
            $table->string('designation')->unique();
            $table->integer('level_id')->unsigned()->index();
            $table->integer('bronz_req')->unsigned()->index();
            $table->integer('silver_req')->unsigned()->index();
            $table->integer('gold_req')->unsigned()->index();
            $table->integer('diamond_req')->unsigned()->index();
            $table->integer('level_req_id')->unsigned()->index();
            $table->integer('level_req_members')->unsigned()->index();
            $table->float('club_percent');
            $table->boolean('is_stto');
            $table->timestamps();
        });
        DB::table('club_masters')->insert(array(
            'designation' => 'Primary Member',
            'level_id' => 0,
            'bronz_req' => 0,
            'silver_req' => 0,
            'gold_req' => 0,
            'diamond_req' => 0,
            'level_req_id' => 0,
            'level_req_members' => 0,
            'club_percent' => 0,
            'is_stto' => 0
        ));
        DB::table('club_masters')->insert(array(
            'designation' => 'Bronze Achiever',
            'level_id' => 1,
            'bronz_req' => 0,
            'silver_req' => 0,
            'gold_req' => 0,
            'diamond_req' => 0,
            'level_req_id' => 1,
            'level_req_members' => 5,
            'club_percent' => 5,
            'is_stto' => 0
        ));
        DB::table('club_masters')->insert(array(
            'designation' => 'Silver Achiever',
            'level_id' => 3,
            'bronz_req' => 5,
            'silver_req' => 0,
            'gold_req' => 0,
            'diamond_req' => 0,
            'level_req_id' => 3,
            'level_req_members' => 125,
            'club_percent' => 5,
            'is_stto' => 1
        ));
        DB::table('club_masters')->insert(array(
            'designation' => 'Gold Achiever',
            'level_id' => 6,
            'bronz_req' => 0,
            'silver_req' => 3,
            'gold_req' => 0,
            'diamond_req' => 0,
            'level_req_id' => 6,
            'level_req_members' => 3375,
            'club_percent' => 3,
            'is_stto' => 1
        ));
        DB::table('club_masters')->insert(array(
            'designation' => 'Diamond Achiever',
            'level_id' => 9,
            'bronz_req' => 0,
            'silver_req' => 0,
            'gold_req' => 3,
            'diamond_req' => 0,
            'level_req_id' => 9,
            'level_req_members' => 91125,
            'club_percent' => 2,
            'is_stto' => 1
        ));
        DB::table('club_masters')->insert(array(
            'designation' => 'Royalty Achiever',
            'level_id' => 12,
            'bronz_req' => 0,
            'silver_req' => 0,
            'gold_req' => 0,
            'diamond_req' => 2,
            'level_req_id' => 12,
            'level_req_members' => 729000,
            'club_percent' => 1,
            'is_stto' => 0
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_masters');
    }
}
