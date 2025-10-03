<x-guest-layout>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.5s ease-out;
        }

        .input-focus:focus {
            border-color: #DA1E1E;
            box-shadow: 0 0 0 3px rgba(218, 30, 30, 0.1);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #DA1E1E 0%, #B01818 100%);
        }

        #login-form, #signup-form {
            opacity: 1;
            transition: opacity 0.2s ease-in-out;
        }
    </style>

    <!-- Header Section -->
    <div class="text-center mb-8 animate-fadeInUp">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full gradient-bg mb-4 shadow-lg">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang</h2>
        <p class="text-gray-600">Masuk ke akun Anda untuk melanjutkan pembelajaran</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Login Form -->
    <div id="login-form" class="bg-white rounded-2xl shadow-xl p-8 animate-fadeInUp">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold" />
                <div class="relative mt-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg input-focus transition-all duration-200"
                        placeholder="nama@email.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-5">
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold" />
                <div class="relative mt-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg input-focus transition-all duration-200"
                        placeholder="Masukkan password">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-5">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#DA1E1E] shadow-sm focus:ring-[#DA1E1E]" name="remember">
                    <span class="ms-2 text-sm text-gray-600 font-medium">{{ __('Ingat saya') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-[#DA1E1E] hover:underline font-medium" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full py-3 gradient-bg text-white rounded-xl hover:shadow-lg transition-all duration-300 font-semibold flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    {{ __('Masuk') }}
                </button>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-[#DA1E1E] hover:underline font-semibold">Daftar sekarang</a>
                </p>
            </div>
        </form>
    </div>

</x-guest-layout>
