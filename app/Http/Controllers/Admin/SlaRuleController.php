<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlaRuleRequest;
use App\Http\Requests\UpdateSlaRuleRequest;
use App\Models\SlaRule;
use App\Models\Priority;

class SlaRuleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SlaRule::class, 'sla_rule');
    }

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

    public function store(StoreSlaRuleRequest $request)
    {
        $validated = $request->validated();
        SlaRule::create($validated);

        return redirect()->route('admin.sla-rules.index')->with('success', 'SLA Rule berhasil dibuat.');
    }

    public function show(SlaRule $slaRule)
    {
        //
    }

    public function edit(SlaRule $slaRule)
    {
        $priorities = Priority::select('id', 'priority_name')->get();
        return view('admin.sla_rules.edit', compact('slaRule', 'priorities'));
    }

    public function update(UpdateSlaRuleRequest $request, SlaRule $slaRule)
    {
        $validated = $request->validated();

        $slaRule->update($validated);
        return redirect()->route('admin.sla-rules.index')->with('success', 'SLA Rule berhasil diperbarui.');
    }

    public function destroy(SlaRule $slaRule)
    {
        $slaRule->delete();
        return redirect()->route('admin.sla-rules.index')->with('success', 'SLA Rule berhasil dihapus.');
    }
}
