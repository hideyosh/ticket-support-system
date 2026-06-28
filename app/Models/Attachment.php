<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable('attachable_type', 'attachable_id', 'uploaded_by', 'original_name', 'stored_name', 'path', 'mime_type', 'sizek')]
class Attachment extends Model
{
    //
}
