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
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('shaba');
            $table->string('bank');
            $table->string('melli_card');
            $table->string('prove_picture');
            $table->string('resume')->nullable();
            $table->string('fname');
            $table->string('lname');
            $table->string('father_name');
            $table->string('melli_code');
            $table->string('province');
            $table->string('city');
            $table->string('street');
            $table->string('number');
            $table->string('postal_code');
            $table->string('address');
            $table->string('site')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('kycs');
    }
};
