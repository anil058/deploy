<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTurnoversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_turnovers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id')->nullable();
            $table->date('turnover_from')->index();
            $table->date('turnover_to')->index();
            $table->string('turnover_type')->index(); //daily/yearly
            $table->float('turnover');
            $table->integer('no_of_recepients');
            $table->float('fraction_leftover'); //remaining amount after distribution
            $table->boolean('is_stto')->default(0)->index();
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
        Schema::dropIfExists('company_turnovers');
    }
}
