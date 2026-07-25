@extends('layouts.app')

@section('page_title', 'Roles & Permissions')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-200">Access Roles Directory</h2>
            <p class="text-xs text-slate-500 mt-1">Manage system security roles and configure mapping of functional permissions.</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Add New Role</span>
        </a>
    </div>

    <!-- Roles Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-4 px-4 w-1/4">Role Name</th>
                    <th class="py-4 px-4 w-2/3">Assigned Permissions</th>
                    <th class="py-4 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50 text-sm text-slate-300">
                @foreach($roles as $role)
                    <tr class="hover:bg-slate-800/10 transition-colors">
                        <td class="py-4 px-4 font-bold text-slate-200 uppercase tracking-wider text-xs">
                            {{ $role->name }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-wrap gap-1.5 max-w-xl">
                                @forelse($role->permissions as $permission)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700/50 text-[10px] font-mono">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <span class="text-xs italic text-slate-600">No permissions assigned</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="p-1.5 bg-indigo-600/10 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded-lg border border-indigo-500/20 transition-all" title="Edit Role Mapping">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </a>

                                @if($role->name !== 'admin')
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg border border-rose-500/20 transition-all" title="Delete Role">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1.5 bg-slate-800/40 text-slate-600 rounded-lg cursor-not-allowed border border-slate-800" title="Admin role cannot be deleted">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())
        <div class="mt-6 border-t border-slate-800/50 pt-6">
            {{ $roles->links() }}
        </div>
    @endif
</div>
@endsection
