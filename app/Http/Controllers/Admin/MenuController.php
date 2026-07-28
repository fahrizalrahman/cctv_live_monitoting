<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MenuController extends Controller
{
    public function index()
    {
        Gate::authorize('manage menus');
        $menus = Menu::whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        Gate::authorize('manage menus');
        $parentMenus = Menu::whereNull('parent_id')->get();
        $permissions = Permission::all();
        return view('admin.menus.create', compact('parentMenus', 'permissions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage menus');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'url' => 'required|string|max:255',
            'order' => 'required|integer',
            'parent_id' => 'nullable|integer|exists:menus,id',
            'permission_name' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['permission_name'])) {
            Permission::firstOrCreate(['name' => $validated['permission_name']]);
        }

        Menu::create($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Menu item successfully created.');
    }

    public function edit(Menu $menu)
    {
        Gate::authorize('manage menus');
        $parentMenus = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();
        $permissions = Permission::all();
        return view('admin.menus.edit', compact('menu', 'parentMenus', 'permissions'));
    }

    public function update(Request $request, Menu $menu)
    {
        Gate::authorize('manage menus');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'url' => 'required|string|max:255',
            'order' => 'required|integer',
            'parent_id' => 'nullable|integer|exists:menus,id',
            'permission_name' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['permission_name'])) {
            Permission::firstOrCreate(['name' => $validated['permission_name']]);
        }

        $menu->update($validated);

        return redirect()->route('admin.menus.index')->with('success', 'Menu item successfully updated.');
    }

    public function destroy(Menu $menu)
    {
        Gate::authorize('manage menus');
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu item successfully deleted.');
    }
}
