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
        Schema::table('join_requests', function (Blueprint $table) {
            $table->string('no_father')->nullable()->after('relation');
            $table->string('death_license')->nullable()->after('no_father');
            $table->string('mother_code')->nullable()->after('death_license');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('join_requests', function (Blueprint $table) {
            //
        });
    }
};
