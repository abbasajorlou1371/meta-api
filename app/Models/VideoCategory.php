<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class VideoCategory extends Model implements Sitemapable
{
    use HasFactory, HasRelationships;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute()
    {
        return config('app.admin_panel_url') . '/uploads/' . $this->image;
    }

    public function getIconUrlAttribute()
    {
        return config('app.admin_panel_url') . '/uploads/' . $this->icon;
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

    public function videos()
    {
        return $this->hasManyThrough(Video::class, VideoSubCategory::class);
    }

    public function views()
    {
        return $this->hasManyDeepFromRelations($this->videos(), (new Video)->views());
    }

    public function likes()
    {
        return $this->hasManyDeepFromRelations($this->videos(), (new Video)->interactions())
            ->where('liked', true);
    }

    public function dislikes()
    {
        return $this->hasManyDeepFromRelations($this->videos(), (new Video)->interactions())
            ->where('liked', false);
    }
}
