<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id');
            $table->string('referal_code');
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('email')->unique();
            $table->string('address')->index();
            $table->string('mobile_no');
            $table->decimal('membership_fee');
            $table->decimal('tax_percent');
            $table->decimal('tax_amount');
            $table->decimal('net_amount');
            $table->string('password');
            $table->boolean('otp_verified')->default(0);
            $table->boolean('closed')->default(0);
            $table->dateTime('expiry_at');
            $table->string('ip')->nullable();
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
        Schema::dropIfExists('temp_members');
    }
}
