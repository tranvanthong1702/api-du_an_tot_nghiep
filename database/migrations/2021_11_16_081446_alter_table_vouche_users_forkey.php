<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableVoucheUsersForkey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voucher_users', function (Blueprint $table) {
            $table->foreignId('voucher_id')->change()
            ->constrained('vouchers')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreignId('user_id')->change()
            ->constrained('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher_users', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropForeign([ 'user_id']);
        });
    }
}
