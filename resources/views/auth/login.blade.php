<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
    <div class="mb-4 text-sm font-medium text-green-400">{{ session('status') }}</div>
    @endif

    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white p-2 mb-4 lg:hidden shadow-md">
            <img src="{{ asset('images/gaf-logo.png') }}" alt="GAF Logo" class="w-full h-full object-contain">
        </div>
        <h2 class="text-2xl font-bold text-white">Sign In</h2>
        <p class="text-slate-400 text-sm mt-1">Access the Command Platform</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email Address" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" class="mt-1.5" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" class="mt-1.5" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-slate-400">Remember me</span>
            </label>

            @if (Route::has('password.request'))
            <a class="text-sm text-blue-400 hover:text-blue-300 transition-colors" href="{{ route('password.request') }}">
                Forgot password?
            </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center py-3">
            Sign In to Command Platform
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-xs text-slate-600">
        AUTHORIZED ACCESS ONLY — ALL ACTIVITIES LOGGED
    </p>
</x-guest-layout>
