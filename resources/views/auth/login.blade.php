<x-guest-layout>
<form method="POST" action="{{ route('login') }}" class="space-y-6">
    @csrf

    <!-- Session Status -->
    @if (session('status'))
        <div class="p-3.5 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Email Address -->
    <div>
        <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i data-lucide="mail" class="w-4 h-4.5"></i>
            </div>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
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
        <div class="flex justify-between items-center mb-2">
            <label for="password" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Password</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">
                    Forgot password?
                </a>
            @endif
        </div>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                <i data-lucide="lock" class="w-4.5 h-4.5"></i>
            </div>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="current-password" 
                   placeholder="••••••••" 
                   class="block w-full pl-11 pr-4 py-3 bg-[#0a0e1a]/80 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all text-sm" />
        </div>
        @if ($errors->has('password'))
            <p class="mt-2 text-xs text-rose-500 font-medium">{{ $errors->first('password') }}</p>
        @endif
    </div>

    <!-- Remember Me -->
    <div class="flex items-center">
        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded bg-[#0a0e1a] border-slate-800 text-indigo-600 focus:ring-indigo-600/30 focus:ring-2 focus:ring-offset-0 focus:outline-none transition-all">
        <label for="remember_me" class="ms-2.5 text-xs text-slate-400 font-medium select-none cursor-pointer">Keep me logged in</label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition-all shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30">
        Sign In to Portal
    </button>
</form>
</x-guest-layout>
