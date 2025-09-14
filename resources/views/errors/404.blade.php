@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Halaman tidak ditemukan'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50 flex items-center justify-center px-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- 404 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Large 404 Text -->
                <div class="text-8xl md:text-9xl font-black text-gray-200 select-none">
                    404
                </div>
                <!-- Magnifying Glass Icon -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-24 h-24 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Oops! Halaman tidak ditemukan
            </h1>
            <p class="text-lg text-gray-600 mb-2">
                Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau tidak pernah ada.
            </p>
            <p class="text-sm text-gray-500">
                Error Code: 404 - Not Found
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Dashboard
            </a>

            <button onclick="window.history.back()"
                    class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg border border-gray-300 shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Halaman Sebelumnya
            </button>
        </div>

        <!-- Helpful Links -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Coba halaman ini:</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center p-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>

                @can('view courses')
                <a href="{{ route('courses.index') }}"
                   class="flex items-center p-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Kelola Kursus
                </a>
                @endcan

                <a href="{{ route('join.form') }}"
                   class="flex items-center p-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m0 0a2 2 0 01-2 2m2-2a2 2 0 002 2M9 5a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path>
                    </svg>
                    Bergabung dengan Token
                </a>

                <a href="{{ route('announcements.index') }}"
                   class="flex items-center p-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.5-1.5A2 2 0 0118 14v-3a6 6 0 10-12 0v3a2 2 0 01-.5 1.5L4 17h5m6 0v1a3 3 0 11-6 0v-1"></path>
                    </svg>
                    Pengumuman
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center p-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profil
                </a>

                <a href="{{ url('/') }}"
                   class="flex items-center p-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Beranda
                </a>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-8 text-center">
            <p class="text-gray-500 text-sm mb-2">Masih butuh bantuan?</p>
            <p class="text-gray-600 text-sm">
                Hubungi administrator sistem atau
                <a href="mailto:admin@example.com" class="text-indigo-600 hover:text-indigo-800 underline">
                    kirim email support
                </a>
            </p>
        </div>
    </div>
</div>
@endsection