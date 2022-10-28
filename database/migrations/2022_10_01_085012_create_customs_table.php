<?php

use App\Models\User;
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
        Schema::create('customs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->longText('profile_code')->nullable();
            $table->string('occupation')->nullable();
            $table->string('education')->nullable();
            $table->string('memory')->nullable();
            $table->string('loved_city')->nullable();
            $table->string('loved_country')->nullable();
            $table->string('loved_language')->nullable();
            $table->text('problem_solving')->nullable();
            $table->text('prediction')->nullable();
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
        Schema::dropIfExists('customs');
    }
};
