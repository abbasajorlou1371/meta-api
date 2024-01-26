<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategory;
use App\Models\VideoSubCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Sitemap\Sitemap;

class SitemapGenerator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Sitemap $sitemap)
    {
        $sitemap->create();

        User::chunk(200, function ($users) use ($sitemap) {
            $sitemap->add($users);
        });

        $sitemap->writeToDisk('ftp', 'citizen-sitemap.xml');

        $sitemap->create()->add(Video::all())
            ->writeToDisk('ftp', 'education_single_video-sitemap.xml');

        $sitemap->create()->add(VideoCategory::all())
            ->writeToDisk('ftp', 'education_category-sitemap.xml');

        $sitemap->create()->add(VideoSubCategory::with('category')->get())
            ->writeToDisk('ftp', 'education_sub_category-sitemap.xml');
    }
}
