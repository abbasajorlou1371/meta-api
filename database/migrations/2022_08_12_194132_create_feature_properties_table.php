<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        Schema::create('feature_properties', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->foreignIdFor(Feature::class);
            $table->text('address');
            $table->integer('density');
            $table->date('date');
            $table->bigInteger('stability');
            $table->string('label')->default('');
            $table->bigInteger('area');
            $table->integer('region');
            $table->string('karbari');
            $table->string('owner');
            $table->string('rgb');
            $table->string('price_psc')->default(0);
            $table->string('price_irr')->default(0);
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
        Schema::dropIfExists('feature_properties');
    }
};
