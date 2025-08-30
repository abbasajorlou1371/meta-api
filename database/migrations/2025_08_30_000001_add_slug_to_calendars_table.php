<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
            $table->index('slug');
        });

        // Backfill slug from title or version_title by replacing spaces with dashes
        DB::table('calendars')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $name = $row->title ?: ($row->version_title ?: null);
                if ($name && empty($row->slug)) {
                    $slug = str_replace(' ', '-', $name);
                    DB::table('calendars')->where('id', $row->id)->update(['slug' => $slug]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};


