<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRechargePointRegister extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recharge_point_registers', function (Blueprint $table) {
            $table->string('tran_type')->after('balance_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recharge_point_registers', function (Blueprint $table) {
            $table->dropColumn(['tran_type']);
        });
    }
}
