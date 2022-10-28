<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEventReportResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_event_report_id',
        'response',
        'responser_name',
    ];

    public function report()
    {
        return $this->belongsTo(UserEventReport::class);
    }
}
