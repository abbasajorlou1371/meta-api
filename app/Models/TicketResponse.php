<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'response',
        'attachment',
        'ticket_id',
        'responser_name',
        'responser_id',
    ];

    /**
     * The ticket that the response belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
