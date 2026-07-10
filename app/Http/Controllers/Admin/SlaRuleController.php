<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SlaRuleRequest;
use App\Models\SlaRule;
use App\Models\Priority;

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

    public function store(SlaRuleRequest $request)
    {
        SlaRule::create($request->validated());
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

    public function update(SlaRuleRequest $request, SlaRule $slaRule)
    {
        $slaRule->update($request->validated());
        return redirect()->route('admin.sla-rules.index')->with('success', 'SLA Rule berhasil diperbarui.');
    }

    public function destroy(SlaRule $slaRule)
    {
        $slaRule->delete();
        return redirect()->route('admin.sla-rules.index')->with('success', 'SLA Rule berhasil dihapus.');
    }
}
