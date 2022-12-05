<?php

namespace App\Models;

use App\Constants\TicketStatus;
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
        return $this->status === TicketStatus::CLOSED;
    }

    public function close()
    {
        $this->update(['status' => TicketStatus::CLOSED]);
    }

    public function markAsResolved()
    {
        $this->update(['status' => TicketStatus::RESOLVED]);
    }
    public function markAsUnresolved()
    {
        $this->update(['status' => TicketStatus::UNRESOLVED]);
    }

}
