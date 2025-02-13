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
        Schema::create('three_dimentional_environment_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('three_dimentional_environment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('entered_at')->default(now());
            $table->dateTime('exited_at')->nullable();
            $table->unsignedBigInteger('duration')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('three_dimentional_environment_interactions');
    }
};
