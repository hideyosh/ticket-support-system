<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('ticket_number', 'title', 'description', 'category_id',
'priority_id', 'status', 'created_by', 'assigned_to', 'due_date', 'resolved_at', 'closed_at')]
class Ticket extends Model
{
    public function creator() : BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedAgent() : BelongsTo {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category() : BelongsTo {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function priority() : BelongsTo {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function comments() : HasMany {
        return $this->hasMany(Comment::class, 'ticket_id');
    }
}
