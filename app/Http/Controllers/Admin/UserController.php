<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CctvGroup;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('manage users');
        $users = User::with(['roles', 'cctvGroups'])->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('manage users');
        $roles = Role::all();
        $cctvGroups = CctvGroup::all();
        return view('admin.users.create', compact('roles', 'cctvGroups'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'cctv_group_ids' => 'nullable|array',
            'cctv_group_ids.*' => 'exists:cctv_groups,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($validated['role']);
        
        if ($validated['role'] === 'viewer' && !empty($validated['cctv_group_ids'])) {
            $user->cctvGroups()->sync($validated['cctv_group_ids']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User successfully created.');
    }

    public function edit(User $user)
    {
        Gate::authorize('manage users');
        $roles = Role::all();
        $cctvGroups = CctvGroup::all();
        return view('admin.users.edit', compact('user', 'roles', 'cctvGroups'));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('manage users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|exists:roles,name',
            'cctv_group_ids' => 'nullable|array',
            'cctv_group_ids.*' => 'exists:cctv_groups,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->syncRoles($validated['role']);
        
        if ($validated['role'] === 'viewer' && !empty($validated['cctv_group_ids'])) {
            $user->cctvGroups()->sync($validated['cctv_group_ids']);
        } else {
            $user->cctvGroups()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'User successfully updated.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('manage users');

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User successfully deleted.');
    }
}
