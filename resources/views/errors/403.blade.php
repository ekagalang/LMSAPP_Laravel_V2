@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Akses ditolak'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-yellow-50 flex items-center justify-center px-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- 403 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Large 403 Text -->
                <div class="text-8xl md:text-9xl font-black text-gray-200 select-none">
                    403
                </div>
                <!-- Lock Icon -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-24 h-24 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Akses Ditolak
            </h1>
            <p class="text-lg text-gray-600 mb-2">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
            </p>
            <p class="text-sm text-gray-500">
                Error Code: 403 - Forbidden
            </p>
        </div>

        <!-- Permission Information -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-lg font-semibold text-gray-900">Informasi Akses</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-orange-50 rounded-lg p-4">
                    <div class="font-medium text-orange-900 mb-2">Role Anda Saat Ini:</div>
                    <div class="text-orange-800">
                        @auth
                            @if(Auth::user()->roles->count() > 0)
                                {{ Auth::user()->roles->pluck('name')->implode(', ') }}
                            @else
                                <span class="text-gray-600">Belum ada role yang ditetapkan</span>
                            @endif
                        @else
                            <span class="text-gray-600">Guest (tidak login)</span>
                        @endauth
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="font-medium text-gray-900 mb-2">Yang Bisa Dilakukan:</div>
                    <div class="text-gray-600">
                        @auth
                            Hubungi administrator untuk meminta akses
                        @else
                            Login terlebih dahulu
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-yellow-600 hover:from-orange-600 hover:to-yellow-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-yellow-600 hover:from-orange-600 hover:to-yellow-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Login Sekarang
                </a>
            @endauth

            <button onclick="window.history.back()"
                    class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg border border-gray-300 shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Halaman Sebelumnya
            </button>
        </div>

        <!-- Common Access Requests -->
        @auth
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Perlu Akses Khusus?</h3>
            <div class="text-left space-y-2 text-sm text-blue-800">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <span><strong>Instructor:</strong> Untuk mengelola kursus dan materi pembelajaran</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span><strong>Event Organizer:</strong> Untuk pemantauan dan pelaporan kursus</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span><strong>Super Admin:</strong> Untuk akses penuh sistem administrasi</span>
                </div>
            </div>
        </div>
        @endauth

        <!-- Contact Support -->
        <div class="text-center">
            <p class="text-gray-500 text-sm mb-2">Butuh bantuan dengan akses?</p>
            <div class="flex flex-col sm:flex-row gap-2 justify-center items-center text-sm">
                <span class="text-gray-600">Hubungi administrator:</span>
                <a href="mailto:admin@example.com" class="text-orange-600 hover:text-orange-800 underline">
                    admin@example.com
                </a>
                <span class="text-gray-400 hidden sm:inline">|</span>
                <span class="text-gray-600">Telepon: +62 xxx-xxxx-xxxx</span>
            </div>
        </div>
    </div>
</div>
@endsection