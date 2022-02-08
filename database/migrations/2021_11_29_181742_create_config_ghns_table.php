<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigGhnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_ghns', function (Blueprint $table) {
            $table->id();
            $table->decimal('provinceID',10,0);
            $table->string('token');
            $table->integer('shopId');
            $table->integer('length');
            $table->integer('width');
            $table->integer('height');
            $table->integer('weight');
            $table->decimal('serviceId',10,0);
            $table->string('service_type_id')->nullable();
            $table->integer('from_districts_id');
            $table->integer('to_districts_id');
            $table->decimal('expected_price',10,0)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_ghns');
    }
}
