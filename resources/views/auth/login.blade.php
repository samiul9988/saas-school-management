<x-guest-layout>
    <h1 class="text-3xl font-extrabold text-gray-900">
        {{ __('Welcome') }} <span class="text-indigo-600">{{ __('Back!') }}</span>
    </h1>
    <p class="mt-2 text-sm text-gray-500">
        {{ __('Login to your account and manage your school with ease. Keep everything organized, secure and under control.') }}
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="bi bi-person"></i>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="{{ __('Email or Username') }}" required autofocus autocomplete="username">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="bi bi-lock"></i>
                </span>
                <input :type="show ? 'text' : 'password'" id="password" name="password"
                       class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="{{ __('Password') }}" required autocomplete="current-password">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me / Forgot Password -->
        <div class="flex items-center justify-between text-sm">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg text-white font-semibold bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 transition">
            <i class="bi bi-box-arrow-in-right"></i> {{ __('Login') }}
        </button>

        <div class="relative text-center">
            <span class="absolute inset-0 flex items-center"><span class="w-full border-t border-gray-200"></span></span>
            <span class="relative bg-white px-3 text-xs uppercase tracking-wider text-gray-400">{{ __('Or') }}</span>
        </div>

        @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
                <i class="bi bi-shield-plus"></i> {{ __('Create New School') }}
            </a>
        @endif
    </form>
</x-guest-layout>
