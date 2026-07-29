<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Label;

class LabelController extends Controller
{
    public function index()
    {
        return response()->json(
            Label::select('id', 'label_name')->orderBy('label_name')->get()
        );
    }
}
