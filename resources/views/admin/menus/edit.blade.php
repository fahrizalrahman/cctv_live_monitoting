@extends('layouts.app')

@section('page_title', 'Edit Menu')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.menus.index') }}" class="p-2 bg-slate-800/40 text-slate-400 hover:text-slate-200 rounded-xl border border-slate-800 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-200">Modify Sidebar Menu</h2>
            <p class="text-xs text-slate-500 mt-1">Configure layout parameter, icon, and visibility logic for this menu link.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.menus.update', $menu->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Menu Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Menu Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $menu->name) }}" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            @error('name')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <!-- Icon -->
            <div>
                <label for="icon" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Lucide Icon Class</label>
                <select id="icon" name="icon"
                        class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm">
                    <option value="">Select an icon...</option>
                    <option value="video" {{ old('icon', $menu->icon) == 'video' ? 'selected' : '' }}>Video (CCTV)</option>
                    <option value="users" {{ old('icon', $menu->icon) == 'users' ? 'selected' : '' }}>Users</option>
                    <option value="layout-dashboard" {{ old('icon', $menu->icon) == 'layout-dashboard' ? 'selected' : '' }}>Dashboard</option>
                    <option value="settings" {{ old('icon', $menu->icon) == 'settings' ? 'selected' : '' }}>Settings</option>
                    <option value="menu" {{ old('icon', $menu->icon) == 'menu' ? 'selected' : '' }}>Menu</option>
                    <option value="shield" {{ old('icon', $menu->icon) == 'shield' ? 'selected' : '' }}>Shield (Security)</option>
                    <option value="map" {{ old('icon', $menu->icon) == 'map' ? 'selected' : '' }}>Map</option>
                    <option value="monitor" {{ old('icon', $menu->icon) == 'monitor' ? 'selected' : '' }}>Monitor</option>
                    <option value="camera" {{ old('icon', $menu->icon) == 'camera' ? 'selected' : '' }}>Camera</option>
                    <option value="server" {{ old('icon', $menu->icon) == 'server' ? 'selected' : '' }}>Server</option>
                    <option value="file-text" {{ old('icon', $menu->icon) == 'file-text' ? 'selected' : '' }}>File Text (Logs)</option>
                    <option value="activity" {{ old('icon', $menu->icon) == 'activity' ? 'selected' : '' }}>Activity</option>
                </select>
                @error('icon')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sorting Order -->
            <div>
                <label for="order" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sorting Order</label>
                <input type="number" id="order" name="order" value="{{ old('order', $menu->order) }}" required
                       class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                @error('order')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- URL Path -->
        <div>
            <label for="url" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">URL Route Path</label>
            <input type="text" id="url" name="url" value="{{ old('url', $menu->url) }}" required
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
                        <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>
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
                <input type="text" list="permission_list" id="permission_name" name="permission_name" value="{{ old('permission_name', $menu->permission_name) }}" placeholder="Select or type new..."
                       class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
                <datalist id="permission_list">
                    @foreach($permissions as $permission)
                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                    @endforeach
                </datalist>
                <span class="block text-[9px] text-slate-500 mt-1">Leave blank for Public. Type a new name to auto-create it.</span>
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
                Update Menu Item
            </button>
        </div>
    </form>
</div>
@endsection
