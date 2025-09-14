@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m0 0a2 2 0 01-2 2m2-2a2 2 0 002 2M9 5a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Bergabung dengan Token</h1>
                <p class="text-gray-600">Masukkan token untuk bergabung ke kursus atau periode</p>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('join.submit') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Token Input -->
                        <div>
                            <label for="token" class="block text-sm font-semibold text-gray-700 mb-2">
                                Token Bergabung
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="token"
                                    name="token"
                                    value="{{ old('token') }}"
                                    class="w-full px-4 py-3 text-center text-lg font-mono font-bold tracking-widest uppercase rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('token') border-red-500 @enderror"
                                    placeholder="ABCD123456"
                                    maxlength="50"
                                    required
                                    autocomplete="off"
                                    style="text-transform: uppercase;">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m0 0a2 2 0 01-2 2m2-2a2 2 0 002 2M9 5a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path>
                                    </svg>
                                </div>
                            </div>

                            @error('token')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="mt-2 text-xs text-gray-500">
                                Token terdiri dari 10 karakter huruf dan angka
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Bergabung Sekarang
                            </span>
                        </button>
                    </form>
                </div>

                <!-- Info Section -->
                <div class="bg-gray-50 px-6 py-4 border-t">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Tentang Token</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• <strong>Token Kursus:</strong> Bergabung langsung ke kursus secara keseluruhan</li>
                                <li>• <strong>Token Periode:</strong> Bergabung ke batch/periode tertentu dalam kursus</li>
                                <li>• Token terdiri dari kombinasi huruf besar dan angka</li>
                                <li>• Dapatkan token dari instructor atau admin kursus</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alternative Access -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Tidak punya token?
                    <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                        Lihat kursus tersedia
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tokenInput = document.getElementById('token');

    // Auto-uppercase input
    tokenInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    // Auto-focus on page load
    tokenInput.focus();
});
</script>
@endpush
@endsection