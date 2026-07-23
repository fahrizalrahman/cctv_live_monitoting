@extends('layouts.app')

@section('page_title', 'Create Role')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.roles.index') }}" class="p-2 bg-slate-800/40 text-slate-400 hover:text-slate-200 rounded-xl border border-slate-800 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-200">Define Access Role</h2>
            <p class="text-xs text-slate-500 mt-1">Create a new access role and select associated user permissions.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
        @csrf

        <!-- Role Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Role Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. operator" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm font-semibold tracking-wider lowercase" />
            <span class="block text-[10px] text-slate-500 mt-1">Write the role name in lowercase (e.g. operator, manager, auditor).</span>
            @error('name')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Permissions Checkboxes -->
        <div>
            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Map Permissions</span>
            <div class="space-y-3 bg-[#0a0e1a]/60 border border-slate-850 p-5 rounded-2xl">
                @foreach($permissions as $permission)
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="permission_{{ $permission->id }}" 
                                   name="permissions[]" 
                                   value="{{ $permission->name }}" 
                                   type="checkbox" 
                                   class="w-4 h-4 rounded bg-[#0a0e1a] border-slate-800 text-indigo-600 focus:ring-indigo-600/30 focus:ring-2 focus:ring-offset-0 focus:outline-none transition-all">
                        </div>
                        <div class="ms-3 text-xs">
                            <label for="permission_{{ $permission->id }}" class="font-semibold text-slate-300 capitalize cursor-pointer select-none">
                                {{ str_replace(' ', ' ', $permission->name) }}
                            </label>
                            <span class="block text-[10px] text-slate-500 font-mono mt-0.5">Key: {{ $permission->name }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('permissions')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 bg-slate-800/40 text-slate-300 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-semibold transition-all">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all">
                Create Access Role
            </button>
        </div>
    </form>
</div>
@endsection
