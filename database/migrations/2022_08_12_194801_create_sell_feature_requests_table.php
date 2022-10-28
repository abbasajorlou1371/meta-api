<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Feature;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_feature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'seller_id');
            $table->foreignIdFor(Feature::class);
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('price_psc')->default(0);
            $table->bigInteger('price_irr')->default(0);
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
        Schema::dropIfExists('sell_feature_requests');
    }
};
