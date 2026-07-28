@extends('layouts.app')

@section('page_title', 'Add Group CCTV')

@section('content')
<div class="bg-[#0d1321]/30 border border-slate-800 rounded-3xl p-6 shadow-xl w-full">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-200">Create New Group</h2>
            <p class="text-xs text-slate-500 mt-1">Add a new CCTV group/category.</p>
        </div>

        <form action="{{ route('admin.groups.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Group Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all placeholder:text-slate-600"
                       placeholder="e.g. Indoor Cameras">
                @error('name')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description <span class="text-slate-500 font-normal">(Optional)</span></label>
                <textarea name="description" id="description" rows="3"
                          class="w-full p-3 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all placeholder:text-slate-600"
                          placeholder="Brief description of this group...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('admin.groups.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
                    Cancel
                </a>
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-600/20 transition-all">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save Group</span>
                </button>
            </div>
        </form>
    </div>
@endsection
