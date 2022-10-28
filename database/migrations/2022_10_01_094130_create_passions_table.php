<?php

use App\Models\User\Custom;
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
        Schema::create('passions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Custom::class);
            $table->string('music')->nullable();
            $table->string('sport_health')->nullable();
            $table->string('art')->nullable();
            $table->string('language_culture')->nullable();
            $table->string('philosophy')->nullable();
            $table->string('animals_nature')->nullable();
            $table->string('aliens')->nullable();
            $table->string('food_cooking')->nullable();
            $table->string('travel_leature')->nullable();
            $table->string('manufacturing')->nullable();
            $table->string('science_technology')->nullable();
            $table->string('space_time')->nullable();
            $table->string('history')->nullable();
            $table->string('politics_economy')->nullable();
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
        Schema::dropIfExists('passions');
    }
};
