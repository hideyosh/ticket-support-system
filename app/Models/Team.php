<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('team_name', 'supervisor_id')]
class Team extends Model
{
    public function supervisor() :BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function agents() :HasMany
    {
        return $this->hasMany(User::class, 'team_id');
    }
}
