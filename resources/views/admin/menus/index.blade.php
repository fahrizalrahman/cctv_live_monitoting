@extends('layouts.app')

@section('page_title', 'Menu Management')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-200">Dynamic Sidebar Menus</h2>
            <p class="text-xs text-slate-500 mt-1">Manage admin panel navigation links, sorting order, icons, and visibility permissions.</p>
        </div>
        <a href="{{ route('admin.menus.create') }}" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Add Menu Item</span>
        </a>
    </div>

    <!-- Menus Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-4 px-4 w-1/4">Menu Name</th>
                    <th class="py-4 px-4">URL Path</th>
                    <th class="py-4 px-4">Order</th>
                    <th class="py-4 px-4">Parent Menu</th>
                    <th class="py-4 px-4">Required Permission</th>
                    <th class="py-4 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50 text-sm text-slate-300">
                @forelse($menus as $menu)
                    <!-- Parent Menu Row -->
                    <tr class="hover:bg-slate-800/10 transition-colors {{ $menu->children->count() > 0 ? 'bg-slate-900/30' : '' }}">
                        <td class="py-4 px-4 font-bold text-slate-200">
                            <div class="flex items-center gap-2.5">
                                <div class="p-1.5 bg-slate-800/60 border border-slate-700/50 rounded-lg text-indigo-400">
                                    @if($menu->icon)
                                        <i data-lucide="{{ $menu->icon }}" class="w-4 h-4"></i>
                                    @else
                                        <i data-lucide="circle" class="w-3.5 h-3.5"></i>
                                    @endif
                                </div>
                                <span>{{ $menu->name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-slate-400">{{ $menu->url }}</td>
                        <td class="py-4 px-4 font-semibold text-slate-400">{{ $menu->order }}</td>
                        <td class="py-4 px-4"><span class="text-xs text-slate-600 italic">None (Root)</span></td>
                        <td class="py-4 px-4">
                            @if($menu->permission_name)
                                <span class="text-xs font-mono text-amber-500 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded">{{ $menu->permission_name }}</span>
                            @else
                                <span class="text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded">Public Access</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.menus.edit', $menu->id) }}" class="p-1.5 bg-indigo-600/10 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded-lg border border-indigo-500/20 transition-all" title="Edit Menu"><i data-lucide="edit-2" class="w-3.5 h-3.5"></i></a>
                                <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this menu item?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg border border-rose-500/20 transition-all" title="Delete Menu"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Child Menu Rows -->
                    @foreach($menu->children as $child)
                        <tr class="hover:bg-slate-800/20 transition-colors bg-[#0a0e1a]/20">
                            <td class="py-3 px-4 pl-12 font-medium text-slate-300">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="corner-down-right" class="w-4 h-4 text-slate-600"></i>
                                    <span>{{ $child->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-400">{{ $child->url }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-500">{{ $child->order }}</td>
                            <td class="py-3 px-4">
                                <span class="text-xs text-slate-400 bg-slate-800/40 px-2 py-1 rounded border border-slate-800">{{ $menu->name }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($child->permission_name)
                                    <span class="text-xs font-mono text-amber-500 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded">{{ $child->permission_name }}</span>
                                @else
                                    <span class="text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded">Public Access</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.menus.edit', $child->id) }}" class="p-1.5 bg-indigo-600/10 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded-lg border border-indigo-500/20 transition-all" title="Edit Menu"><i data-lucide="edit-2" class="w-3.5 h-3.5"></i></a>
                                    <form action="{{ route('admin.menus.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this menu item?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-600/10 text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg border border-rose-500/20 transition-all" title="Delete Menu"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">
                            <i data-lucide="menu" class="w-8 h-8 mx-auto mb-3 text-slate-600"></i>
                            <p class="font-medium text-slate-400">No menu items found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
