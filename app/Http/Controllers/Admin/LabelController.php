<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::latest()->paginate(15);
        return view('admin.labels.index', compact('labels'));
    }

    public function create()
    {
        return view('admin.labels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label_name' => [
                'required',
                'string',
                'max:255',
                'unique:labels,label_name',
            ],
        ]);

        Label::create($validated);
        return redirect()->route('admin.labels.index')->with('success', 'Label berhasil dibuat.');
    }

    public function show(Label $label)
    {
        $tickets = $label->tickets()->with(['creator', 'assignedAgent', 'category', 'priority'])->latest()->take(5)->get();
        return view('admin.labels.show', compact('label', 'tickets'));
    }

    public function edit(Label $label)
    {
        return view('admin.labels.edit', compact('label'));
    }

    public function update(Request $request, Label $label)
    {
        $validated = $request->validate([
            'label_name' => [
                'required',
                'string',
                'max:255',
                'unique:labels,label_name,' . $label->id,
            ],
        ]);

        $label->update($validated);
        return redirect()->route('admin.labels.index')->with('success', 'Label berhasil diperbarui.');
    }

    public function destroy(Label $label)
    {
        $label->delete();
        return redirect()->route('admin.labels.index')->with('success', 'Label berhasil dihapus.');
    }
}
