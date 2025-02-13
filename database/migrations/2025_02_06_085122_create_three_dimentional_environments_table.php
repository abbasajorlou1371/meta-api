<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('three_dimentional_environments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->text('environment_url');
            $table->integer('entry_price_psc')->default(0);
            $table->integer('entry_price_irr')->default(0);
            $table->string('entry_level_limit')->nullable();
            $table->string('entry_level_limit_type')->nullable();
            $table->smallInteger('entry_level_rank_limit')->nullable();
            $table->bigInteger('total_entry_count')->default(0);
            $table->integer('online_entry_count')->default(0);
            $table->mediumText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('three_dimentional_environments');
    }
};
