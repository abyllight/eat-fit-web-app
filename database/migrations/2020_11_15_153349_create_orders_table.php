<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('amo_id');
            $table->integer('courier_id')->nullable();
            $table->integer('courier1_id')->nullable();
            $table->integer('courier2_id')->nullable();
            $table->string('name')->nullable();
            $table->string('tag')->nullable();
            $table->string('size')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('time')->nullable();
            $table->string('time1')->nullable();
            $table->string('time2')->nullable();
            $table->string('time1_old')->nullable();
            $table->string('time2_old')->nullable();
            $table->string('yaddress')->nullable();
            $table->string('yaddress1')->nullable();
            $table->string('yaddress2')->nullable();
            $table->string('yaddress1_old')->nullable();
            $table->string('yaddress2_old')->nullable();
            $table->longText('address')->nullable();
            $table->longText('address1')->nullable();
            $table->longText('address2')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('lat1')->nullable();
            $table->string('lng1')->nullable();
            $table->string('lat2')->nullable();
            $table->string('lng2')->nullable();
            $table->integer('interval')->nullable();
            $table->longText('addition')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('active')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
