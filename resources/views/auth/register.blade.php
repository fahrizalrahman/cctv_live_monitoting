<x-guest-layout>
<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    <!-- Name -->
    <div>
        <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Full Name</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i data-lucide="user" class="w-4.5 h-4.5"></i>
            </div>
            <input id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="John Doe" 
                   class="block w-full pl-11 pr-4 py-3 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
        </div>
        @if ($errors->has('name'))
            <p class="mt-2 text-xs text-rose-500 font-medium">{{ $errors->first('name') }}</p>
        @endif
    </div>

    <!-- Email Address -->
    <div>
        <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i data-lucide="mail" class="w-4.5 h-4.5"></i>
            </div>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="name@company.com" 
                   class="block w-full pl-11 pr-4 py-3 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
        </div>
        @if ($errors->has('email'))
            <p class="mt-2 text-xs text-rose-500 font-medium">{{ $errors->first('email') }}</p>
        @endif
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i data-lucide="lock" class="w-4.5 h-4.5"></i>
            </div>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="new-password" 
                   placeholder="••••••••" 
                   class="block w-full pl-11 pr-4 py-3 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
        </div>
        @if ($errors->has('password'))
            <p class="mt-2 text-xs text-rose-500 font-medium">{{ $errors->first('password') }}</p>
        @endif
    </div>

    <!-- Confirm Password -->
    <div>
        <label for="password_confirmation" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i data-lucide="lock" class="w-4.5 h-4.5"></i>
            </div>
            <input id="password_confirmation" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password" 
                   placeholder="••••••••" 
                   class="block w-full pl-11 pr-4 py-3 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
        </div>
        @if ($errors->has('password_confirmation'))
            <p class="mt-2 text-xs text-rose-500 font-medium">{{ $errors->first('password_confirmation') }}</p>
        @endif
    </div>

    <!-- Submit and Link -->
    <div class="space-y-4 pt-2">
        <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition-all shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30">
            Create Free Account
        </button>
        
        <div class="text-center text-xs text-slate-500">
            Already registered? 
            <a href="{{ route('login') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">
                Sign In instead
            </a>
        </div>
    </div>
</form>
</x-guest-layout>
