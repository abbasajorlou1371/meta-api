<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEventReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_event_id',
        'suspecious_citizen',
        'event_description',
        'status',
        'closed'
    ];

    public function event()
    {
        return $this->belongsTo(UserEvent::class);
    }

    public function responses()
    {
        return $this->hasMany(UserEventReportResponse::class);
    }
}
