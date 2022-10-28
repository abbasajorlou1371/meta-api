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
        Schema::table('feature_properties', function (Blueprint $table) {
            $table->integer('minimum_price_percentage')->default(100)->after('price_irr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feature_properties', function (Blueprint $table) {
            $table->dropColumn('minimum_price_percentage');
        });
    }
};
