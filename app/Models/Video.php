<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;

class Video extends Model implements Sitemapable
{
    use HasFactory;

    protected $guarded = [];

    protected $withCount = ['views', 'likes', 'dislikes'];

    protected $appends = ['image_url', 'video_url'];

    public function getImageUrlAttribute()
    {
        return config('app.admin_panel_url') . '/uploads/' . $this->image;
    }

    public function getVideoUrlAttribute()
    {
        return config('app.admin_panel_url') . '/uploads/' . $this->fileName;
    }

    public function toSitemapTag(): Url|string|array
    {
        return [
            Url::create('https://rgb.irpsc.com/fa/education/watch/rgb-video-' . $this->id)
                ->setLastModificationDate(Carbon::create($this->updated_at))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8)
                ->addVideo($this->image_url, $this->title, $this->description, $this->video_url),
            Url::create('https://rgb.irpsc.com/en/education/watch/rgb-video-' . $this->id)
                ->setLastModificationDate(Carbon::create($this->updated_at))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8)
                ->addVideo($this->image_url, $this->title, $this->description, $this->video_url),
        ];
    }

    public function incrementViews()
    {
        $this->views()->create([
            'ip_address' => request()->ip()
        ]);
    }

    public function interactions(): MorphMany
    {
        return $this->morphMany(Interaction::class, 'likeable');
    }

    public function likes()
    {
        return $this->interactions()->where('liked', 1);
    }

    public function dislikes()
    {
        return $this->interactions()->where('liked', 0);
    }

    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(CommentReport::class, 'commentable');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(VideoSubCategory::class, 'video_sub_category_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_code', 'code');
    }
}
