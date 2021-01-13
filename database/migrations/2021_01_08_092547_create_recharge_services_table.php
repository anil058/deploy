<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargeServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->index();
            $table->string('description')->index();
            $table->integer('company_id')->nullable()->unsigned()->index();
            $table->boolean('active')->index();
            $table->integer('type')->nullable()->unsigned()->index();
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
        Schema::dropIfExists('recharge_services');
    }
}
