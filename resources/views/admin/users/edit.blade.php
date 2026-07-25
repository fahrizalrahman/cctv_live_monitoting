@extends('layouts.app')

@section('page_title', 'Edit User')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="p-2 bg-slate-800/40 text-slate-400 hover:text-slate-200 rounded-xl border border-slate-800 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-200">Modify User & Role</h2>
            <p class="text-xs text-slate-500 mt-1">Edit profile details and assign system authorization roles.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- User Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            @error('name')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            @error('email')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Assignment -->
        <div>
            <label for="role" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Assign System Role</label>
            <select id="role" name="role" required
                    class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm">
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-800/40 text-slate-300 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-semibold transition-all">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all">
                Update User Profile
            </button>
        </div>
    </form>
</div>
@endsection
