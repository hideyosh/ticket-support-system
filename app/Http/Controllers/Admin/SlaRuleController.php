<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaRule;
use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SlaRuleController extends Controller
{
    public function index()
    {
        $slaRules = SlaRule::with('priority')->latest()->paginate(15);
        return view('admin.sla_rules.index', compact('slaRules'));
    }

    public function create()
    {
        $priorities = Priority::select('id', 'priority_name')->get();
        return view('admin.sla_rules.create', compact('priorities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'priority_id' => [
                'required',
                Rule::exists('priorities', 'id'),
                Rule::unique('sla_rules', 'priority_id'),
            ],
            'response_time' => ['required', 'integer', 'min:1'],
            'resolution_time' => ['required', 'integer', 'min:1', 'gt:response_time'],
        ]);

        SlaRule::create($validated);
        return redirect()->route('admin.sla-rules.index')->with('success', 'SLA Rule berhasil dibuat.');
    }

    public function show(SlaRule $slaRule)
    {
        $slaRule->load('priority');
        return view('admin.sla_rules.show', compact('slaRule'));
    }

    public function edit(SlaRule $slaRule)
    {
        $priorities = Priority::select('id', 'priority_name')->get();
        return view('admin.sla_rules.edit', compact('slaRule', 'priorities'));
    }

    public function update(Request $request, SlaRule $slaRule)
    {
        $validated = $request->validate([
            'priority_id' => [
                'required',
                Rule::exists('priorities', 'id'),
                Rule::unique('sla_rules', 'priority_id')->ignore($slaRule->id),
            ],
            'response_time' => ['required', 'integer', 'min:1'],
            'resolution_time' => ['required', 'integer', 'min:1', 'gt:response_time'],
        ]);

        $slaRule->update($validated);
        return redirect()->route('admin.sla-rules.index')->with('success', 'SLA Rule berhasil diperbarui.');
    }

    public function destroy(SlaRule $slaRule)
    {
        $slaRule->delete();
        return redirect()->route('admin.sla-rules.index')->with('success', 'SLA Rule berhasil dihapus.');
    }
}
