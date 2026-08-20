@extends('layouts.app')

@section('page_title', 'Create User')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="p-2 bg-slate-800/40 text-slate-400 hover:text-slate-200 rounded-xl border border-slate-800 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-200">Create New User</h2>
            <p class="text-xs text-slate-500 mt-1">Add a new user and assign system authorization roles.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

        <!-- User Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            @error('name')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            @error('email')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
            <input type="password" id="password" name="password" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
            @error('password')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                   class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
        </div>

        <!-- Role Assignment -->
        <div>
            <label for="role" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Assign System Role</label>
            <select id="role" name="role" required onchange="toggleCctvGroup()"
                    class="block w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm">
                <option value="">-- Select Role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- CCTV Group Assignment (Only for Viewer) -->
        <div id="cctv-group-container" style="display: none;">
            <label for="cctv_group_ids" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">CCTV Groups (For Viewer Role)</label>
            <select id="cctv_group_ids" name="cctv_group_ids[]" multiple
                    class="block w-full">
                @foreach($cctvGroups as $group)
                    <option value="{{ $group->id }}" {{ in_array($group->id, (array) old('cctv_group_ids', [])) ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
            @error('cctv_group_ids')
                <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-800/40 text-slate-300 border border-slate-850 hover:bg-slate-800 rounded-xl text-xs font-semibold transition-all">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-indigo-600/20 transition-all">
                Create User
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Dark Theme adjustments to match Tailwind */
    .select2-container--default .select2-selection--multiple {
        background-color: rgba(10, 14, 26, 0.8) !important;
        border-color: #1e293b !important;
        border-radius: 0.75rem !important;
        padding: 0.35rem 0.5rem !important;
        min-height: 44px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #4f46e5 !important;
        border: 1px solid #4338ca !important;
        color: white !important;
        border-radius: 0.5rem !important;
        padding: 4px 10px 4px 24px !important;
        font-size: 0.8rem !important;
        margin-top: 2px !important;
        position: relative !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #e0e7ff !important;
        position: absolute !important;
        left: 6px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        border-right: none !important;
        font-size: 1.1rem !important;
        padding: 0 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: transparent !important;
        color: white !important;
    }
    .select2-dropdown {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #e2e8f0 !important;
        border-radius: 0.75rem !important;
        overflow: hidden !important;
        margin-top: 4px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #1e293b !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #4f46e5 !important;
        color: white !important;
    }
    .select2-search__field {
        color: #e2e8f0 !important;
        background: transparent !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function toggleCctvGroup() {
        var roleSelect = document.getElementById('role');
        var cctvGroupContainer = document.getElementById('cctv-group-container');
        if (roleSelect.value === 'viewer') {
            cctvGroupContainer.style.display = 'block';
        } else {
            cctvGroupContainer.style.display = 'none';
        }
    }
    
    $(document).ready(function() {
        toggleCctvGroup();
        
        $('#cctv_group_ids').select2({
            placeholder: "-- Select CCTV Groups --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
