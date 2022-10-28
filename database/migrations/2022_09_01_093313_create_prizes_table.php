<?php

use App\Models\Level\Level;
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
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Level::class);
            $table->bigInteger('psc')->nullable();
            $table->bigInteger('blue')->nullable();
            $table->bigInteger('red')->nullable();
            $table->bigInteger('yellow')->nullable();
            $table->tinyInteger('union_license')->nullable();
            $table->integer('union_members_count')->nullable();
            $table->tinyInteger('observing_license')->nullable();
            $table->tinyInteger('gate_license')->nullable();
            $table->tinyInteger('lawyer_license')->nullable();
            $table->tinyInteger('city_counsil_entry')->nullable();
            $table->bigInteger('special_residence_property')->nullable();
            $table->bigInteger('property_on_area')->nullable();
            $table->tinyInteger('judge_entry')->nullable();
            $table->float('satisfaction');
            $table->integer('effect');
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
        Schema::dropIfExists('prizes');
    }
};
