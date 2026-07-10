<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LabelRequest;
use App\Models\Label;

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

    public function store(LabelRequest $request)
    {
        Label::create($request->validated());
        return redirect()->route('admin.labels.index')->with('success', 'Label berhasil dibuat.');
    }

    public function show(Label $label)
    {
        return view('admin.labels.show', compact('label'));
    }

    public function edit(Label $label)
    {
        return view('admin.labels.edit', compact('label'));
    }

    public function update(LabelRequest $request, Label $label)
    {
        $label->update($request->validated());
        return redirect()->route('admin.labels.index')->with('success', 'Label berhasil diperbarui.');
    }

    public function destroy(Label $label)
    {
        $label->delete();
        return redirect()->route('admin.labels.index')->with('success', 'Label berhasil dihapus.');
    }
}
