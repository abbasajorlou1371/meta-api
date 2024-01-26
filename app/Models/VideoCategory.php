<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;

class VideoCategory extends Model implements Sitemapable
{
    use HasFactory;

    public function getImageUrlAttribute()
    {
        return config('app.admin_panel_url') . '/uploads/' . $this->image;
    }

    public function toSitemapTag(): Url|string|array
    {
        return [
            Url::create('https://rgb.irpsc.com/fa/education/category/' . $this->slug)
                ->setLastModificationDate(Carbon::create($this->updated_at))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8)->addImage($this->image_url),
            Url::create('https://rgb.irpsc.com/en/education/category/' . $this->slug)
                ->setLastModificationDate(Carbon::create($this->updated_at))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8)->addImage($this->image_url)
        ];
    }

    public function subCategories()
    {
        return $this->hasMany(VideoSubCategory::class);
    }
}
