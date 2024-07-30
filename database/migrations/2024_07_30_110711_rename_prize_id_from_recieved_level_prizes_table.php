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
        Schema::table('recieved_level_prizes', function (Blueprint $table) {
            $table->renameColumn('prize_id', 'level_prize_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recieved_level_prizes', function (Blueprint $table) {
            $table->renameColumn('level_prize_id', 'prize_id');
        });
    }
};
