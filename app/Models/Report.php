<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Report
 *
 * @property int $id
 * @property string $title
 * @property string $page
 * @property string $text
 * @property string $subject
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report wherePage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserId($value)
 * @mixin \Eloquent
 */
class Report extends Model
{
    use HasFactory;
    protected $table = "reports";
    protected $fillable = [
        'subject',
        'title',
        'content',
        'url',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
