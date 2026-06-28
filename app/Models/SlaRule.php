<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable('priority_id', 'response_time', 'resolution_time')]
class SlaRule extends Model
{
    //
}
