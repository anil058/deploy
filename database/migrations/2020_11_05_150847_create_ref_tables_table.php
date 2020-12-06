<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_tables', function (Blueprint $table) {
            $table->id();
            $table->integer('Level');
            $table->integer('total_members');
            $table->float('business_amount');
            $table->float('level_income_percent');
            $table->float('max_level_income');
            $table->string('reward');
            $table->integer('designation_id');
            $table->string('club_income_on');
            $table->float('club_income_percent');
            $table->float('sponser_income_on');
            $table->float('sponser_income_percent');
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
        Schema::dropIfExists('ref_tables');
    }
}
