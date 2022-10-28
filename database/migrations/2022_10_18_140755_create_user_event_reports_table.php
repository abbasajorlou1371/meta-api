<?php

use App\Models\User\UserEvent;
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
        Schema::create('user_event_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(UserEvent::class);
            $table->string('suspecious_citizen')->nullable();
            $table->text('event_description');
            $table->tinyInteger('status')->default(0);
            $table->boolean('closed')->default(0);
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
        Schema::dropIfExists('user_event_reports');
    }
};
