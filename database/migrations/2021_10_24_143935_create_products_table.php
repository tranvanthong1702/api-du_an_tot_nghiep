<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('cate_id');
            $table->string('name',255)->unique();
            $table->string('image',255);
            $table->decimal('price',12,0);
            $table->tinyInteger('sale')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->date('expiration_date')->nullable();
            $table->text('desc_short');
            $table->text('description');
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
        Schema::dropIfExists('products');
    }
}
