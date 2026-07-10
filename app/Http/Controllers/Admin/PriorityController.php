<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PriorityRequest;
use App\Models\Priority;

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

    public function store(PriorityRequest $request)
    {
        Priority::create($request->validated());
        return redirect()->route('admin.priorities.index')->with('success', 'Prioritas berhasil dibuat.');
    }

    public function show(Priority $priority)
    {
        return view('admin.priorities.show', compact('priority'));
    }

    public function edit(Priority $priority)
    {
        return view('admin.priorities.edit', compact('priority'));
    }

    public function update(PriorityRequest $request, Priority $priority)
    {
        $priority->update($request->validated());
        return redirect()->route('admin.priorities.index')->with('success', 'Prioritas berhasil diperbarui.');
    }

    public function destroy(Priority $priority)
    {
        $priority->delete();
        return redirect()->route('admin.priorities.index')->with('success', 'Prioritas berhasil dihapus.');
    }
}
