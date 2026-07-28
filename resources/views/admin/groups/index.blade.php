@extends('layouts.app')

@section('page_title', 'Group CCTV')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-200">Group CCTV Management</h2>
            <p class="text-xs text-slate-500 mt-1">Manage CCTV groups and categories.</p>
        </div>
        <a href="{{ route('admin.groups.create') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Add Group</span>
        </a>
    </div>

    <!-- Groups Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-4 px-4">Name</th>
                    <th class="py-4 px-4">Description</th>
                    <th class="py-4 px-4">Total CCTVs</th>
                    <th class="py-4 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50 text-sm text-slate-300">
                @forelse($groups as $group)
                    <tr class="hover:bg-slate-800/10 transition-colors">
                        <td class="py-4 px-4 font-bold text-slate-200">
                            {{ $group->name }}
                        </td>
                        <td class="py-4 px-4 text-slate-400">
                            {{ Str::limit($group->description, 50) ?? '-' }}
                        </td>
                        <td class="py-4 px-4 font-semibold text-indigo-400">
                            {{ $group->cctvs_count }}
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.groups.edit', $group->id) }}" class="p-1.5 bg-indigo-600/10 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded-lg border border-indigo-500/20 transition-all" title="Edit Group">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </a>
                                
                                <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg border border-rose-500/20 transition-all" title="Delete Group">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-500">
                            <i data-lucide="layers" class="w-8 h-8 mx-auto mb-3 text-slate-600"></i>
                            <p class="font-medium text-slate-400">No groups found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($groups->hasPages())
        <div class="mt-6 border-t border-slate-800/50 pt-6">
            {{ $groups->links() }}
        </div>
    @endif
</div>
@endsection
