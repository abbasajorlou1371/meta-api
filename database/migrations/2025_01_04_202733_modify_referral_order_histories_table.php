<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referral_order_histories', function (Blueprint $table) {
            $table->dropColumn('referral_id');
            $table->renameColumn('reference_id', 'user_id');
            $table->renameColumn('referrer_id', 'referral_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referral_order_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('referral_id')->nullable();
            $table->renameColumn('user_id', 'reference_id');
            $table->renameColumn('referral_id', 'referrer_id');
        });
    }
};
