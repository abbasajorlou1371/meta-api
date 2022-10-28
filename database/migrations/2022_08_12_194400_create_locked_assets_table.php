<?php

use App\Models\BuyFeatureRequest;
use App\Models\Feature;
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
        Schema::create('locked_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->onDelete('cascade');
            $table->foreignIdFor(BuyFeatureRequest::class)->onDelete('cascade');
            $table->foreignIdFor(Feature::class)->onDelete('cascade');
            $table->bigInteger('psc');
            $table->bigInteger('irr');
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
        Schema::dropIfExists('locked_assets');
    }
};
