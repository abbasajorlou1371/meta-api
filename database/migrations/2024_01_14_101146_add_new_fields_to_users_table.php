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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral')->nullable()->after('referal_link');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('token_type')->nullable();
            $table->integer('expires_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'access_token',
                'refresh_token',
                'token_type',
                'expires_in',
                'referral',
            ]);
        });
    }
};
