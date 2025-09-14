@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Halaman telah kadaluarsa'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-indigo-50 flex items-center justify-center px-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- 419 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Large 419 Text -->
                <div class="text-8xl md:text-9xl font-black text-gray-200 select-none">
                    419
                </div>
                <!-- Clock Icon -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-24 h-24 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Halaman Telah Kadaluarsa
            </h1>
            <p class="text-lg text-gray-600 mb-2">
                Sesi keamanan telah habis. Untuk alasan keamanan, form yang Anda isi perlu dimuat ulang.
            </p>
            <p class="text-sm text-gray-500">
                Error Code: 419 - Page Expired (CSRF Token Mismatch)
            </p>
        </div>

        <!-- Security Information -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="text-lg font-semibold text-gray-900">Fitur Keamanan</span>
                </div>
            </div>

            <div class="text-left bg-purple-50 rounded-lg p-4 mb-4">
                <h4 class="font-medium text-purple-900 mb-2">Mengapa ini terjadi?</h4>
                <ul class="text-sm text-purple-800 space-y-1">
                    <li>• Form telah dibuka terlalu lama (lebih dari 2 jam)</li>
                    <li>• Browser melakukan refresh atau reload halaman</li>
                    <li>• Terdapat masalah dengan token keamanan</li>
                    <li>• Sesi login telah berubah di tab browser lain</li>
                </ul>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="font-medium text-gray-900 mb-1">Waktu Kejadian</div>
                    <div class="text-gray-600">{{ now()->format('d M Y, H:i:s') }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="font-medium text-gray-900 mb-1">Status Keamanan</div>
                    <div class="text-green-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Normal & Aman
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
            <button onclick="window.location.reload()"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Muat Ulang Form
            </button>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg border border-gray-300 shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Dashboard
            </button>
        </div>

        <!-- What to do next -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Langkah Selanjutnya:</h3>
            <div class="text-left space-y-3 text-sm text-blue-800">
                <div class="flex items-start">
                    <div class="flex items-center justify-center w-6 h-6 bg-blue-200 text-blue-800 rounded-full text-xs font-bold mr-3 mt-0.5 flex-shrink-0">1</div>
                    <span><strong>Muat Ulang Halaman:</strong> Klik tombol "Muat Ulang Form" untuk mendapatkan form yang baru</span>
                </div>
                <div class="flex items-start">
                    <div class="flex items-center justify-center w-6 h-6 bg-blue-200 text-blue-800 rounded-full text-xs font-bold mr-3 mt-0.5 flex-shrink-0">2</div>
                    <span><strong>Isi Ulang Data:</strong> Data yang sudah Anda isi sebelumnya mungkin hilang, silakan isi kembali</span>
                </div>
                <div class="flex items-start">
                    <div class="flex items-center justify-center w-6 h-6 bg-blue-200 text-blue-800 rounded-full text-xs font-bold mr-3 mt-0.5 flex-shrink-0">3</div>
                    <span><strong>Submit Cepat:</strong> Setelah mengisi, langsung submit untuk menghindari kadaluarsa lagi</span>
                </div>
            </div>
        </div>

        <!-- Tips to Prevent -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-yellow-900 mb-3">Tips Mencegah Kadaluarsa:</h3>
            <div class="text-left grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-yellow-800">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Isi form secara bertahap dan langsung submit</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Hindari membuka form terlalu lama</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Siapkan data sebelum membuka form</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Jangan refresh browser saat mengisi form</span>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="text-center">
            <p class="text-gray-500 text-sm mb-2">Masalah terus terjadi?</p>
            <div class="flex flex-col sm:flex-row gap-2 justify-center items-center text-sm">
                <span class="text-gray-600">Hubungi support:</span>
                <a href="mailto:support@example.com" class="text-purple-600 hover:text-purple-800 underline">
                    support@example.com
                </a>
                <span class="text-gray-400 hidden sm:inline">|</span>
                <span class="text-gray-600">Chat: Available 24/7</span>
            </div>
        </div>
    </div>
</div>
@endsection