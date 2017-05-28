<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Commodity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('region_id');
            $table->string('name');
            $table->integer('price');
            $table->bigInteger('flow')->comment('流量');
            $table->bigInteger('expiration')->comment('时间戳，过期时间');
            $table->string('introduction')->nullable()->comment('套餐介绍');
            $table->softDeletes();
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
        Schema::dropIfExists('commodities');
    }
}
