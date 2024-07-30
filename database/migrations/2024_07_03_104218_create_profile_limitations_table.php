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
        Schema::create('profile_limitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('limiter_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('limited_user_id')->constrained('users')->onDelete('cascade');
            $table->text('options');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('profile_limitations');
    }
};
