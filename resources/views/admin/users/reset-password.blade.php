<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reset Password User: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- User Info Card --}}
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
                        <h3 class="text-lg font-semibold mb-2">Informasi User</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama:</label>
                                <p class="text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email:</label>
                                <p class="text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Role:</label>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->getRoleNames() as $roleName)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $roleName }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Password Reset Form --}}
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Peringatan</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Anda akan mengubah password untuk user <strong>{{ $user->name }}</strong>. Pastikan untuk memberitahu user tentang password baru ini.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div>
                                <x-input-label for="password" :value="__('Password Baru')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                <p class="text-xs text-gray-500 mt-1">Password minimal 8 karakter</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>

                        {{-- Password Strength Indicator --}}
                        <div class="mt-4">
                            <div class="text-sm text-gray-600 mb-2">Kekuatan Password:</div>
                            <div id="password-strength" class="h-2 bg-gray-200 rounded-full">
                                <div id="strength-bar" class="h-2 rounded-full transition-all duration-300 w-0"></div>
                            </div>
                            <div id="strength-text" class="text-xs text-gray-500 mt-1"></div>
                        </div>

                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 px-4 py-2 border border-gray-300 rounded-md">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Reset Password') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = calculatePasswordStrength(password);
                updateStrengthIndicator(strength);
            });

            function calculatePasswordStrength(password) {
                let score = 0;
                if (!password) return score;

                // Length
                if (password.length >= 8) score += 25;
                if (password.length >= 12) score += 25;

                // Contains lowercase
                if (/[a-z]/.test(password)) score += 15;

                // Contains uppercase
                if (/[A-Z]/.test(password)) score += 15;

                // Contains numbers
                if (/\d/.test(password)) score += 10;

                // Contains special characters
                if (/[^A-Za-z0-9]/.test(password)) score += 10;

                return Math.min(score, 100);
            }

            function updateStrengthIndicator(strength) {
                strengthBar.style.width = strength + '%';
                
                if (strength < 30) {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
                    strengthText.textContent = 'Lemah';
                    strengthText.className = 'text-xs text-red-500 mt-1';
                } else if (strength < 60) {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
                    strengthText.textContent = 'Sedang';
                    strengthText.className = 'text-xs text-yellow-500 mt-1';
                } else if (strength < 80) {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-blue-500';
                    strengthText.textContent = 'Kuat';
                    strengthText.className = 'text-xs text-blue-500 mt-1';
                } else {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
                    strengthText.textContent = 'Sangat Kuat';
                    strengthText.className = 'text-xs text-green-500 mt-1';
                }
            }
        });
    </script>
    @endpush
</x-app-layout>