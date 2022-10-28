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
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('department', [
                'technical_support',
                'citizens_safety',
                'investment',
                'inspection',
                'protection',
                'ztb'
            ])->after('reciever_id')->nullable();
            $table->tinyInteger('importance')->after('department')->default(0);
            $table->integer('code')->after('importance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['department', 'importance']);
        });
    }
};
