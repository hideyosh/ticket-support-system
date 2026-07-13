<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('category_name')]
class Category extends Model
{
    public function tickets() : HasMany {
        return $this->hasMany(Ticket::class, 'category_id');
    }
}
