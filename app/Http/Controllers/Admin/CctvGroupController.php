<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CctvGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CctvGroupController extends Controller
{
    public function index()
    {
        Gate::authorize('manage groups');
        $groups = CctvGroup::withCount('cctvs')->paginate(10);
        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        Gate::authorize('manage groups');
        return view('admin.groups.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage groups');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        CctvGroup::create($validated);
        return redirect()->route('admin.groups.index')->with('success', 'Group created successfully.');
    }

    public function edit(CctvGroup $group)
    {
        Gate::authorize('manage groups');
        return view('admin.groups.edit', compact('group'));
    }

    public function update(Request $request, CctvGroup $group)
    {
        Gate::authorize('manage groups');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $group->update($validated);
        return redirect()->route('admin.groups.index')->with('success', 'Group updated successfully.');
    }

    public function destroy(CctvGroup $group)
    {
        Gate::authorize('manage groups');
        $group->delete();
        return redirect()->route('admin.groups.index')->with('success', 'Group deleted successfully.');
    }
}
