<?php

use App\Models\Feature;
use App\Models\IsicCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_physical_information', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Feature::class)->unique()->constrained()->cascadeOnDelete();
            $table->string('group_name', 255);
            $table->string('active_company', 255);
            $table->string('physical_address', 500);
            $table->string('physical_postal_code', 10);
            $table->string('postal_address', 10);
            $table->string('establishment_goal', 1000);
            $table->foreignIdFor(IsicCode::class)->constrained('isic_codes')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_physical_information');
    }
};
