<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('priority_name')]
class Priority extends Model
{
    public function ticket() : HasMany {
        return $this->hasMany(Ticket::class, 'priority_id');
    }
}
