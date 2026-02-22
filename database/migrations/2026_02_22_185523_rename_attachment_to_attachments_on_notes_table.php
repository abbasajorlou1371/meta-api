<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('content');
        });

        // Migrate existing single attachment to attachments array
        $notes = DB::table('notes')->whereNotNull('attachment')->where('attachment', '!=', '')->get();
        foreach ($notes as $note) {
            DB::table('notes')->where('id', $note->id)->update([
                'attachments' => json_encode([$note->attachment]),
            ]);
        }

        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('content');
        });

        $notes = DB::table('notes')->whereNotNull('attachments')->get();
        foreach ($notes as $note) {
            $urls = json_decode($note->attachments, true);
            $first = is_array($urls) && count($urls) > 0 ? $urls[0] : null;
            DB::table('notes')->where('id', $note->id)->update(['attachment' => $first]);
        }

        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
