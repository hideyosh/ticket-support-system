<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function index()
    {
        $priorities = Priority::latest()->paginate(15);
        return view('admin.priorities.index', compact('priorities'));
    }

    public function create()
    {
        return view('admin.priorities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'priority_name' => [
                'required',
                'string',
                'max:255',
                'unique:priorities,priority_name',
            ],
        ]);

        Priority::create($validated);
        return redirect()->route('admin.priorities.index')->with('success', 'Prioritas berhasil dibuat.');
    }

    public function show(Priority $priority)
    {
        $tickets = $priority->tickets()->with(['creator', 'assignedAgent', 'category'])->latest()->take(5)->get();
        return view('admin.priorities.show', compact('priority', 'tickets'));
    }

    public function edit(Priority $priority)
    {
        return view('admin.priorities.edit', compact('priority'));
    }

    public function update(Request $request, Priority $priority)
    {
        $validated = $request->validate([
            'priority_name' => [
                'required',
                'string',
                'max:255',
                'unique:priorities,priority_name,' . $priority->id,
            ],
        ]);

        $priority->update($validated);
        return redirect()->route('admin.priorities.index')->with('success', 'Prioritas berhasil diperbarui.');
    }

    public function destroy(Priority $priority)
    {
        $priority->delete();
        return redirect()->route('admin.priorities.index')->with('success', 'Prioritas berhasil dihapus.');
    }
}
