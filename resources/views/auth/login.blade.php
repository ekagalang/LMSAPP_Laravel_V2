<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Tabs -->
    <div class="mb-6">
        <div class="flex border-b border-gray-200">
            <button onclick="showTab('login')" id="login-tab" class="flex-1 py-2 px-4 text-center font-medium text-blue-600 border-b-2 border-blue-600 bg-white transition-colors duration-200">
                Masuk
            </button>
            <button onclick="showTab('signup')" id="signup-tab" class="flex-1 py-2 px-4 text-center font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 transition-colors duration-200">
                Daftar
            </button>
        </div>
    </div>

    <!-- Login Form -->
    <div id="login-form">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- 🍯 HONEYPOT FIELDS untuk LOGIN -->
            <div style="position: absolute; left: -5000px; top: -5000px;">
                <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="url" name="company_url" tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="text" name="phone_number" tabindex="-1" autocomplete="nope" aria-hidden="true">
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3">
                    {{ __('Masuk') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <!-- Signup Form -->
    <div id="signup-form" class="hidden">
        @if (Route::has('register'))
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- 🍯 HONEYPOT FIELDS untuk REGISTER -->
                <div style="position: absolute; left: -5000px; top: -5000px;">
                    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <input type="url" name="company_url" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <input type="text" name="phone_number" tabindex="-1" autocomplete="nope" aria-hidden="true">
                    <input type="text" name="username" tabindex="-1" autocomplete="off" aria-hidden="true">
                </div>

                <div>
                    <x-input-label for="register_name" :value="__('Nama')" />
                    <x-text-input id="register_name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="register_email" :value="__('Email')" />
                    <x-text-input id="register_email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="register_password" :value="__('Password')" />
                    <x-text-input id="register_password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Terms and Conditions -->
                <div class="block mt-4">
                    <label for="terms" class="inline-flex items-center">
                        <input id="terms" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="terms" required>
                        <span class="ms-2 text-sm text-gray-600">
                            Saya setuju dengan
                            <a href="#" class="underline text-blue-600 hover:text-blue-900">syarat dan ketentuan</a>
                        </span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>
                        {{ __('Daftar') }}
                    </x-primary-button>
                </div>
            </form>
        @else
            <div class="text-center py-8">
                <p class="text-gray-600">Fitur pendaftaran belum tersedia.</p>
            </div>
        @endif
    </div>

    <script>
        function showTab(tabName) {
            // Hide all forms with smooth transition
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');
            const loginTab = document.getElementById('login-tab');
            const signupTab = document.getElementById('signup-tab');

            // Add fade out effect
            loginForm.style.opacity = '0';
            signupForm.style.opacity = '0';

            setTimeout(() => {
                // Hide all forms
                loginForm.classList.add('hidden');
                signupForm.classList.add('hidden');

                // Reset tab styles
                loginTab.classList.remove('text-blue-600', 'border-blue-600');
                loginTab.classList.add('text-gray-500', 'border-transparent');
                signupTab.classList.remove('text-blue-600', 'border-blue-600');
                signupTab.classList.add('text-gray-500', 'border-transparent');

                // Show selected form and update tab style
                if (tabName === 'login') {
                    loginForm.classList.remove('hidden');
                    loginTab.classList.remove('text-gray-500', 'border-transparent');
                    loginTab.classList.add('text-blue-600', 'border-blue-600');

                    // Focus on email field
                    setTimeout(() => {
                        document.getElementById('email').focus();
                    }, 50);
                } else {
                    signupForm.classList.remove('hidden');
                    signupTab.classList.remove('text-gray-500', 'border-transparent');
                    signupTab.classList.add('text-blue-600', 'border-blue-600');

                    // Focus on name field
                    setTimeout(() => {
                        document.getElementById('register_name').focus();
                    }, 50);
                }

                // Fade in effect
                setTimeout(() => {
                    if (tabName === 'login') {
                        loginForm.style.opacity = '1';
                    } else {
                        signupForm.style.opacity = '1';
                    }
                }, 50);

            }, 150);
        }

        // Handle form validation errors - show appropriate tab
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there are registration errors and show signup tab
            @if($errors->any() && (old('name') || old('password_confirmation')))
                showTab('signup');
            @endif

            // Add smooth transitions
            document.getElementById('login-form').style.transition = 'opacity 0.2s ease-in-out';
            document.getElementById('signup-form').style.transition = 'opacity 0.2s ease-in-out';
        });

        // Password strength indicator for signup
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('register_password');
            if (passwordField) {
                passwordField.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    // Check password strength
                    if (password.length >= 8) strength++;
                    if (/[A-Z]/.test(password)) strength++;
                    if (/[a-z]/.test(password)) strength++;
                    if (/[0-9]/.test(password)) strength++;
                    if (/[^A-Za-z0-9]/.test(password)) strength++;

                    // Update border color based on strength
                    this.classList.remove('border-red-300', 'border-yellow-300', 'border-green-300');
                    if (password.length > 0) {
                        if (strength < 3) {
                            this.classList.add('border-red-300');
                        } else if (strength < 4) {
                            this.classList.add('border-yellow-300');
                        } else {
                            this.classList.add('border-green-300');
                        }
                    }
                });
            }
        });
    </script>

    <style>
        #login-form, #signup-form {
            opacity: 1;
            transition: opacity 0.2s ease-in-out;
        }
        
        /* 🍯 Extra honeypot protection dengan CSS yang lebih canggih */
        input[name="website"],
        input[name="company_url"], 
        input[name="phone_number"],
        input[name="username"] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            width: 0 !important;
            height: 0 !important;
        }
    </style>
</x-guest-layout>