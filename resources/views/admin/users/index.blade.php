@extends('layouts.app')

@section('page_title', 'User Management')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-200">System Users</h2>
            <p class="text-xs text-slate-500 mt-1">Manage user roles and system privileges to authorize platform operators.</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-4 px-4">Name</th>
                    <th class="py-4 px-4">Email Address</th>
                    <th class="py-4 px-4">Role</th>
                    <th class="py-4 px-4">Registered Date</th>
                    <th class="py-4 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50 text-sm text-slate-300">
                @foreach($users as $user)
                    <tr class="hover:bg-slate-800/10 transition-colors">
                        <td class="py-4 px-4 font-bold text-slate-200">{{ $user->name }}</td>
                        <td class="py-4 px-4 font-mono text-xs text-slate-400">{{ $user->email }}</td>
                        <td class="py-4 px-4">
                            @php
                                $role = $user->roles->first()?->name ?? 'None';
                                $roleClasses = [
                                    'admin' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                    'operator' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'viewer' => 'bg-slate-700/20 text-slate-400 border-slate-700/30'
                                ];
                                $badgeClass = $roleClasses[$role] ?? 'bg-slate-800 text-slate-500 border-slate-800/30';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider border {{ $badgeClass }}">
                                {{ $role }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-xs text-slate-500">{{ $user->created_at->format('M d, Y H:i') }}</td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="p-1.5 bg-indigo-600/10 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded-lg border border-indigo-500/20 transition-all" title="Edit Role">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </a>

                                @if($user->id !== Auth::id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg border border-rose-500/20 transition-all" title="Delete User">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1.5 bg-slate-800/40 text-slate-600 rounded-lg cursor-not-allowed border border-slate-800" title="Self account deletion disabled">
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
    @if($users->hasPages())
        <div class="mt-6 border-t border-slate-800/50 pt-6">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
