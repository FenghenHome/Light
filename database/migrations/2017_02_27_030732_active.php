<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Active extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->nullable();
            $table->integer('commodity_id')->nullable();
            $table->integer('price')->nullable();
            $table->string('content')->nullable();
            $table->string('code')->nullable();
            $table->bigInteger('flow')->nullable();
            $table->integer('expired_time')->nullable();
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
        Schema::dropIfExists('actives');
    }
}
