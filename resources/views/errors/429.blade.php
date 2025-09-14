@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Terlalu banyak permintaan'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50 flex items-center justify-center px-4">
    <div class="max-w-2xl mx-auto text-center">
        <!-- 429 Illustration -->
        <div class="mb-8">
            <div class="relative">
                <!-- Large 429 Text -->
                <div class="text-8xl md:text-9xl font-black text-gray-200 select-none">
                    429
                </div>
                <!-- Speed Limit Icon -->
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
                Permintaan Terlalu Cepat
            </h1>
            <p class="text-lg text-gray-600 mb-2">
                Anda telah melakukan terlalu banyak permintaan dalam waktu singkat. Mohon tunggu sejenak sebelum mencoba lagi.
            </p>
            <p class="text-sm text-gray-500">
                Error Code: 429 - Too Many Requests (Rate Limited)
            </p>
        </div>

        <!-- Rate Limiting Information -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-lg font-semibold text-gray-900">Batas Kecepatan Akses</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                <div class="bg-red-50 rounded-lg p-3">
                    <div class="font-medium text-red-900 mb-1">Waktu Tunggu</div>
                    <div class="text-red-800 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span id="countdown">60 detik</span>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="font-medium text-gray-900 mb-1">Status Sistem</div>
                    <div class="text-green-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Normal
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg p-4">
                <h4 class="font-medium text-orange-900 mb-2">Mengapa ada batasan ini?</h4>
                <ul class="text-sm text-orange-800 space-y-1">
                    <li>• Melindungi server dari overload</li>
                    <li>• Memastikan performa optimal untuk semua pengguna</li>
                    <li>• Mencegah penyalahgunaan sistem</li>
                    <li>• Menjaga stabilitas aplikasi</li>
                </ul>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
            <button id="retryButton"
                    onclick="retryAfterCooldown()"
                    disabled
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-400 to-orange-500 text-white font-semibold rounded-lg shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span id="buttonText">Tunggu...</span>
            </button>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg border border-gray-300 shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Rate Limiting Rules -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Batas Akses Sistem:</h3>
            <div class="text-left grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                <div class="bg-white rounded-lg p-3">
                    <div class="font-medium text-blue-900 mb-2">Web Browsing</div>
                    <div class="text-blue-700">60 halaman per menit</div>
                </div>
                <div class="bg-white rounded-lg p-3">
                    <div class="font-medium text-blue-900 mb-2">Form Submission</div>
                    <div class="text-blue-700">10 form per menit</div>
                </div>
                <div class="bg-white rounded-lg p-3">
                    <div class="font-medium text-blue-900 mb-2">File Upload</div>
                    <div class="text-blue-700">5 upload per menit</div>
                </div>
                <div class="bg-white rounded-lg p-3">
                    <div class="font-medium text-blue-900 mb-2">API Calls</div>
                    <div class="text-blue-700">100 request per menit</div>
                </div>
            </div>
        </div>

        <!-- Best Practices -->
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold text-green-900 mb-3">Tips Penggunaan Optimal:</h3>
            <div class="text-left space-y-2 text-sm text-green-800">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Tunggu beberapa detik antara setiap aksi</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Hindari refresh berulang-ulang</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Kerjakan satu tugas dalam satu waktu</span>
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pastikan koneksi internet stabil</span>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="text-center">
            <p class="text-gray-500 text-sm mb-2">Mengalami masalah berulang?</p>
            <div class="flex flex-col sm:flex-row gap-2 justify-center items-center text-sm">
                <span class="text-gray-600">Laporkan ke technical support:</span>
                <a href="mailto:tech@example.com" class="text-red-600 hover:text-red-800 underline">
                    tech@example.com
                </a>
                <span class="text-gray-400 hidden sm:inline">|</span>
                <span class="text-gray-600">WhatsApp: +62 xxx-xxxx-xxxx</span>
            </div>
        </div>
    </div>
</div>

<script>
let countdownSeconds = 60;
let countdownInterval;

function startCountdown() {
    countdownInterval = setInterval(() => {
        countdownSeconds--;
        document.getElementById('countdown').textContent = countdownSeconds + ' detik';
        document.getElementById('buttonText').textContent = `Tunggu ${countdownSeconds}s`;

        if (countdownSeconds <= 0) {
            clearInterval(countdownInterval);
            enableRetryButton();
        }
    }, 1000);
}

function enableRetryButton() {
    const button = document.getElementById('retryButton');
    button.disabled = false;
    button.classList.remove('from-red-400', 'to-orange-500');
    button.classList.add('from-green-500', 'to-green-600', 'hover:from-green-600', 'hover:to-green-700');
    document.getElementById('buttonText').textContent = 'Coba Lagi Sekarang';
    document.getElementById('countdown').textContent = 'Siap!';
}

function retryAfterCooldown() {
    if (countdownSeconds <= 0) {
        window.location.reload();
    }
}

// Start countdown when page loads
document.addEventListener('DOMContentLoaded', function() {
    startCountdown();
});
</script>
@endsection