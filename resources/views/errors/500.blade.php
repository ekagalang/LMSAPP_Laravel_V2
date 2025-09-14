@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server mengalami gangguan'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-pink-50 flex items-center justify-center px-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- 500 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Large 500 Text -->
                <div class="text-8xl md:text-9xl font-black text-gray-200 select-none">
                    500
                </div>
                <!-- Server Error Icon -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-24 h-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Ups! Server mengalami gangguan
            </h1>
            <p class="text-lg text-gray-600 mb-2">
                Terjadi kesalahan internal pada server. Tim teknis kami sedang bekerja untuk memperbaikinya.
            </p>
            <p class="text-sm text-gray-500">
                Error Code: 500 - Internal Server Error
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
            <button onclick="window.location.reload()"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Muat Ulang Halaman
            </button>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg border border-gray-300 shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Dashboard
            </button>
        </div>

        <!-- Status Information -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse mr-2"></div>
                    <span class="text-sm font-medium text-red-600">Server Status: Mengalami Gangguan</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="font-medium text-gray-900 mb-1">Waktu Kejadian</div>
                    <div class="text-gray-600">{{ now()->format('d M Y, H:i:s') }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="font-medium text-gray-900 mb-1">Status Tim</div>
                    <div class="text-orange-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Sedang Investigasi
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="font-medium text-gray-900 mb-1">Estimasi Perbaikan</div>
                    <div class="text-blue-600">15-30 menit</div>
                </div>
            </div>
        </div>

        <!-- What to do next -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Apa yang dapat Anda lakukan?</h3>
            <div class="text-left space-y-2 text-sm text-blue-800">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Tunggu beberapa menit dan coba muat ulang halaman</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Periksa koneksi internet Anda</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Coba akses halaman lain di website ini</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Jika masalah berlanjut, hubungi administrator</span>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="text-center">
            <p class="text-gray-500 text-sm mb-2">Masalah tidak kunjung selesai?</p>
            <div class="flex flex-col sm:flex-row gap-2 justify-center items-center text-sm">
                <span class="text-gray-600">Hubungi tim support:</span>
                <a href="mailto:support@example.com" class="text-red-600 hover:text-red-800 underline">
                    support@example.com
                </a>
                <span class="text-gray-400 hidden sm:inline">|</span>
                <span class="text-gray-600">WhatsApp: +62 xxx-xxxx-xxxx</span>
            </div>
        </div>
    </div>
</div>

<script>
// Auto refresh after 30 seconds
setTimeout(function() {
    if (confirm('Halaman akan dimuat ulang secara otomatis. Lanjutkan?')) {
        window.location.reload();
    }
}, 30000);
</script>
@endsection