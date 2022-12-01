<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'reciever_id',
        'attachment',
        'status',
        'department',
        'importance',
        'code',
        'responser_name'
    ];

    public function sender() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reciever() {
        return $this->belongsTo(User::class, 'reciever_id');
    }

    public function responses() {
        return $this->hasMany(TicketResponse::class);
    }

    public function isClosed()
    {
        return $this->status === 3;
    }

}
