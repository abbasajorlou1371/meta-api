<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Feature;
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
        Schema::create('trades', function(Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Feature::class);
            $table->foreignIdFor(User::class, 'buyer_id');
            $table->foreignIdFor(User::class, 'seller_id');
            $table->unsignedBigInteger('irr_amount')->nullable();
            $table->unsignedBigInteger('psc_amount')->nullable();
            $table->date('date');
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
        //
    }
};
