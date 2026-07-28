@extends('layouts.app')

@section('page_title', 'Master CCTV')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-200">CCTV Device Directory</h2>
            <p class="text-xs text-slate-500 mt-1">Manage network parameters, login credentials, and stream URLs for all CCTV cameras.</p>
        </div>
        <div class="flex items-center justify-end gap-3 shrink-0">
            <form action="{{ route('admin.cctvs.index') }}" method="GET" class="flex items-center gap-2 shrink-0">
                <div class="relative w-48 shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    </div>
                    <select name="cctv_group_id" onchange="this.form.submit()" class="block w-full pl-9 pr-8 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-xs appearance-none cursor-pointer">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('cctv_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-slate-500">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
                @if(request()->filled('cctv_group_id'))
                    <a href="{{ route('admin.cctvs.index') }}" class="p-2.5 bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white rounded-xl transition-all border border-rose-500/20 shrink-0" title="Clear Filter">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.cctvs.create') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all shrink-0">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Add New CCTV</span>
            </a>
        </div>
    </div>

    <!-- CCTV Devices Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-4 px-4">Name & Group</th>
                    <th class="py-4 px-4">Network Info</th>
                    <th class="py-4 px-4">Channel</th>
                    <th class="py-4 px-4">Credentials</th>
                    <th class="py-4 px-4">Stream URL</th>
                    <th class="py-4 px-4">Status</th>
                    <th class="py-4 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50 text-sm text-slate-300">
                @forelse($cctvs as $cctv)
                    <tr class="hover:bg-slate-800/10 transition-colors">
                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-200 block">{{ $cctv->name }}</span>
                            @if($cctv->group)
                                <div class="inline-flex items-center gap-1.5 mt-1.5 px-2 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-md" title="Group">
                                    <i data-lucide="folder-open" class="w-3 h-3 text-indigo-400"></i>
                                    <span class="text-[10px] font-medium text-indigo-300 tracking-wide uppercase">{{ $cctv->group->name }}</span>
                                </div>
                            @endif
                            <span class="text-[10px] text-slate-500 block font-mono mt-1.5">Lat: {{ $cctv->latitude }} | Lng: {{ $cctv->longitude }}</span>
                        </td>
                        <td class="py-4 px-4 font-mono text-xs">
                            <span class="text-slate-200">{{ $cctv->ip }}</span>:<span class="text-indigo-400">{{ $cctv->port }}</span>
                        </td>
                        <td class="py-4 px-4 font-semibold text-slate-400">{{ $cctv->channel }}</td>
                        <td class="py-4 px-4">
                            @if($cctv->username)
                                <span class="block text-xs font-mono text-slate-400">User: {{ $cctv->username }}</span>
                                <span class="block text-[10px] text-slate-600 font-mono mt-0.5">Pass: ••••••••</span>
                            @else
                                <span class="text-xs italic text-slate-600">None</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="max-w-[200px] truncate font-mono text-xs text-slate-400" title="{{ $cctv->stream_url }}">
                                {{ $cctv->stream_url }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            @if($cctv->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <span class="h-1.5 w-1.5 bg-emerald-400 rounded-full"></span>
                                    <span>Active</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                    <span class="h-1.5 w-1.5 bg-rose-500 rounded-full"></span>
                                    <span>Inactive</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.cctvs.edit', $cctv->id) }}" class="p-1.5 bg-indigo-600/10 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded-lg border border-indigo-500/20 transition-all" title="Edit">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </a>
                                
                                <form action="{{ route('admin.cctvs.destroy', $cctv->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this CCTV device?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg border border-rose-500/20 transition-all" title="Delete">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">
                            <i data-lucide="camera-off" class="w-8 h-8 mx-auto mb-3 text-slate-600"></i>
                            <p class="font-medium text-slate-400">No CCTV devices found.</p>
                            <p class="text-xs text-slate-600 mt-1">Add a new CCTV device to start streaming.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($cctvs->hasPages())
        <div class="mt-6 border-t border-slate-800/50 pt-6">
            {{ $cctvs->links() }}
        </div>
    @endif
</div>
@endsection
