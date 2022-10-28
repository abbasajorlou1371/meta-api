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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->boolean('announcements_sms')->default(false);
            $table->boolean('announcements_email')->default(false);
            $table->boolean('reports_sms')->default(false);
            $table->boolean('reports_email')->default(false);
            $table->boolean('login_verification_sms')->default(false);
            $table->boolean('login_verification_email')->default(false);
            $table->boolean('transactions_sms')->default(false);
            $table->boolean('transactions_email')->default(false);
            $table->boolean('trades_sms')->default(false);
            $table->boolean('trades_email')->default(false);
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
        Schema::dropIfExists('general_settings');
    }
};
