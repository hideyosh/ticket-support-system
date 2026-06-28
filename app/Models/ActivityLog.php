<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable('user_id', 'ticket_id', 'action', 'field', 'old_value', 'new_value')]
class ActivityLog extends Model
{
    //
}
