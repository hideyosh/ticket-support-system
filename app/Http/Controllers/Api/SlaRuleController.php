<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SlaRule;

class SlaRuleController extends Controller
{
    public function index()
    {
        return response()->json(
            SlaRule::with('priority:id,priority_name')->get()
        );
    }
}
