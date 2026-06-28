<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable('ticket_id', 'user_id', 'body', 'type')]
class Comment extends Model
{
    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket() : BelongsTo {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
