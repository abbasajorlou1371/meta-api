<?php

use App\Models\User\UserEventReport;
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
        Schema::create('user_event_report_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(UserEventReport::class);
            $table->text('response');
            $table->string('responser_name');
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
        Schema::dropIfExists('user_event_report_responses');
    }
};
