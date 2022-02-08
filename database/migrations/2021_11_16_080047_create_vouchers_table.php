<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('classify_voucher_id');
            $table->string('title');
            $table->tinyInteger('sale');
            $table->string('customer_type',255);
            $table->decimal('condition',12,0);
            $table->tinyInteger('expiration');
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('planning')->default(0);
            $table->tinyInteger('times');
            $table->date('start_day');
            $table->date('end_day')->nullable();
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
        Schema::dropIfExists('vouchers');
    }
}
