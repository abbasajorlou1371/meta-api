<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->bigInteger('psc')->default(0);
            $table->bigInteger('irr')->default(0);
            $table->bigInteger('red')->default(0);
            $table->bigInteger('blue')->default(0);
            $table->bigInteger('yellow')->default(0);
            $table->float('satisfaction')->default(0.1);
            $table->bigInteger('effect')->default(1);
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
        Schema::dropIfExists('assets');
    }
};
