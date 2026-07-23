@extends('layouts.app')

@section('page_title', 'Create Menu')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.menus.index') }}" class="p-2 bg-slate-800/40 text-slate-400 hover:text-slate-200 rounded-xl border border-slate-800 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-200">Add Sidebar Menu</h2>
            <p class="text-xs text-slate-500 mt-1">Configure layout parameter, icon, and visibility logic for a new sidebar menu link.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.menus.store') }}" class="space-y-6">
        @csrf

        <!-- Menu Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Menu Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Master CCTV" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            @error('name')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <!-- Icon -->
            <div>
                <label for="icon" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Lucide Icon Class</label>
                <input type="text" id="icon" name="icon" value="{{ old('icon') }}" placeholder="e.g. video"
                       class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                <span class="block text-[9px] text-slate-500 mt-1">Provide a valid Lucide icon name (e.g., video, users, settings, menu).</span>
                @error('icon')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sorting Order -->
            <div>
                <label for="order" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sorting Order</label>
                <input type="number" id="order" name="order" value="{{ old('order', 0) }}" required
                       class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                @error('order')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- URL Path -->
        <div>
            <label for="url" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">URL Route Path</label>
            <input type="text" id="url" name="url" value="{{ old('url') }}" placeholder="e.g. /admin/cctvs" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm font-mono text-xs" />
            @error('url')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <!-- Parent Menu -->
            <div>
                <label for="parent_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Parent Menu (Optional)</label>
                <select id="parent_id" name="parent_id"
                        class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm">
                    <option value="">None (Root Menu)</option>
                    @foreach($parentMenus as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Permission Visibility -->
            <div>
                <label for="permission_name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Restrict by Permission</label>
                <select id="permission_name" name="permission_name"
                        class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm">
                    <option value="">Public / Authenticated (No restriction)</option>
                    @foreach($permissions as $permission)
                        <option value="{{ $permission->name }}" {{ old('permission_name') === $permission->name ? 'selected' : '' }}>
                            {{ $permission->name }}
                        </option>
                    @endforeach
                </select>
                @error('permission_name')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.menus.index') }}" class="px-5 py-2.5 bg-slate-800/40 text-slate-300 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-semibold transition-all">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all">
                Save Menu Item
            </button>
        </div>
    </form>
</div>
@endsection
