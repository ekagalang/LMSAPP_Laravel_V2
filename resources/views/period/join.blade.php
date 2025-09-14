<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Bergabung ke Periode') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Masukkan token untuk bergabung ke periode kursus</p>
            </div>
            <div class="hidden md:flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m-2 2h.01M5 15H3a2 2 0 01-2-2V9a2 2 0 012-2h2m0 4h.01m0 4H3a2 2 0 01-2-2v-2a2 2 0 012-2h2m12 0h.01m0 4h.01"></path>
                </svg>
                Join Period
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m-2 2h.01M5 15H3a2 2 0 01-2-2V9a2 2 0 012-2h2m0 4h.01m0 4H3a2 2 0 01-2-2v-2a2 2 0 012-2h2m12 0h.01m0 4h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Bergabung dengan Token</h3>
                    <p class="text-indigo-100">Masukkan token yang diberikan instructor untuk bergabung ke periode kursus</p>
                </div>

                <div class="p-8">
                    <!-- Info Cards -->
                    <div class="grid md:grid-cols-2 gap-4 mb-8">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-900">Token Format</p>
                                    <p class="text-xs text-blue-700">8 karakter huruf/angka</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-900">Akses Langsung</p>
                                    <p class="text-xs text-green-700">Bergabung otomatis</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Join Form -->
                    <form action="{{ route('join.submit') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="token" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m-2 2h.01"></path>
                                </svg>
                                Token Bergabung *
                            </label>

                            <div class="relative">
                                <input type="text"
                                       name="token"
                                       id="token"
                                       class="w-full px-4 py-4 text-lg font-mono uppercase rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('token') border-red-500 @enderror"
                                       value="{{ old('token') }}"
                                       placeholder="Contoh: ABC12345"
                                       maxlength="10"
                                       required
                                       autocomplete="off"
                                       style="letter-spacing: 2px;">

                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m-2 2h.01"></path>
                                    </svg>
                                </div>
                            </div>

                            @error('token')
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="text-gray-500 text-sm mt-2">
                                💡 Token diberikan oleh instructor atau dapat ditemukan di informasi periode
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Dashboard
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Bergabung Sekarang
                            </button>
                        </div>
                    </form>

                    <!-- Help Section -->
                    <div class="mt-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <h4 class="text-gray-800 font-medium text-sm mb-2">Bantuan</h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Token biasanya berupa 8 karakter kombinasi huruf dan angka</li>
                            <li>• Pastikan Anda sudah login sebelum memasukkan token</li>
                            <li>• Hubungi instructor jika token tidak valid atau periode sudah penuh</li>
                            <li>• Setiap token hanya berlaku untuk satu periode kursus tertentu</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tokenInput = document.getElementById('token');

            // Auto uppercase and filter input
            tokenInput.addEventListener('input', function(e) {
                const value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                e.target.value = value;
            });

            // Auto focus on page load
            tokenInput.focus();
        });
    </script>
    @endpush
</x-app-layout>