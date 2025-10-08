<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Back Button -->
                        <a href="{{ url()->previous() }}"
                           class="inline-flex items-center p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>

                        <div>
                            <h1 class="text-3xl font-bold">{{ $quiz->title }}</h1>
                            <p class="text-indigo-100 mt-1">Siap untuk mengetes pengetahuan Anda?</p>
                        </div>
                    </div>

                    @if($quiz->enable_leaderboard)
                        <a href="{{ route('quizzes.leaderboard', $quiz) }}"
                           class="hidden md:flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors text-sm font-medium">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Lihat Full Leaderboard
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="grid lg:grid-cols-3 gap-8">

                <!-- Left Column: Quiz Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Quiz Info Card -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold">Instruksi Kuis</h2>
                                    <p class="text-blue-100 text-sm">Baca dengan teliti sebelum memulai</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body with Scroll -->
                        <div class="p-6 max-h-96 overflow-y-auto custom-scrollbar">
                            @if($quiz->description)
                                <div class="prose prose-gray max-w-none mb-6">
                                    <div class="text-gray-700 leading-relaxed">
                                        {!! $quiz->description !!}
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-600 mb-6">
                                    <p>Kuis ini akan menguji pemahaman Anda tentang materi yang telah dipelajari. Jawablah setiap pertanyaan dengan teliti dan jujur.</p>
                                </div>
                            @endif

                            <!-- Quiz Rules -->
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                <div class="flex items-start">
                                    <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-amber-800 mb-2">Perhatian Penting</h4>
                                        <ul class="text-sm text-amber-700 space-y-1">
                                            <li>• Pastikan koneksi internet Anda stabil</li>
                                            <li>• Setelah memulai, kuis tidak dapat dihentikan atau diulang</li>
                                            <li>• Jawab semua pertanyaan sebelum waktu habis</li>
                                            <li>• Hasil akan langsung ditampilkan setelah selesai</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Total Questions -->
                        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-2xl font-bold text-gray-900">{{ $quiz->questions->count() }}</p>
                                <p class="text-xs text-gray-600 mt-1">Pertanyaan</p>
                            </div>
                        </div>

                        <!-- Total Points -->
                        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-2xl font-bold text-gray-900">{{ $quiz->total_marks }}</p>
                                <p class="text-xs text-gray-600 mt-1">Total Poin</p>
                            </div>
                        </div>

                        <!-- Pass Marks -->
                        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-2xl font-bold text-gray-900">{{ $quiz->passing_percentage }}%</p>
                                <p class="text-xs text-gray-600 mt-1">Nilai Lulus</p>
                            </div>
                        </div>

                        <!-- Time Limit -->
                        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($quiz->time_limit)
                                    <p class="text-2xl font-bold text-gray-900">{{ $quiz->time_limit }}</p>
                                    <p class="text-xs text-gray-600 mt-1">Menit</p>
                                @else
                                    <p class="text-xl font-bold text-gray-900">∞</p>
                                    <p class="text-xs text-gray-600 mt-1">Tidak Terbatas</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Start Button -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">Siap Memulai?</h3>
                            <p class="text-gray-600 text-sm mt-1">Pastikan Anda sudah membaca semua instruksi</p>
                        </div>

                        <form action="{{ route('quizzes.start_attempt', $quiz) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ __('Mulai Kerjakan Sekarang') }}</span>
                            </button>
                        </form>

                        <p class="text-xs text-gray-500 text-center mt-3">
                            Dengan mengklik tombol di atas, Anda menyetujui untuk mengerjakan kuis ini dengan jujur
                        </p>
                    </div>
                </div>

                <!-- Right Column: Leaderboard Only -->
                <div class="space-y-6">
                    <!-- Leaderboard -->
                    @if($quiz->enable_leaderboard)
                        @php
                            $leaderboard = $quiz->getLeaderboardWithBestAttempts(10);
                        @endphp

                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl shadow-xl border-2 border-yellow-300 overflow-hidden">
                            <!-- Leaderboard Header -->
                            <div class="bg-gradient-to-r from-yellow-400 to-amber-500 p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-white/30 rounded-xl flex items-center justify-center mr-3">
                                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold">🏆 Leaderboard</h3>
                                            <p class="text-xs text-yellow-100">Top 10 Peserta Terbaik</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Leaderboard with Scroll -->
                            <div class="p-6">
                                @if($leaderboard->count() > 0)
                                    <div class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar pr-2">
                                        @foreach($leaderboard as $entry)
                                            @php
                                                $isTop3 = $entry['rank'] <= 3;
                                                $rankBg = match($entry['rank']) {
                                                    1 => 'bg-gradient-to-r from-yellow-400 to-yellow-500',
                                                    2 => 'bg-gradient-to-r from-gray-300 to-gray-400',
                                                    3 => 'bg-gradient-to-r from-orange-400 to-orange-500',
                                                    default => 'bg-gray-200'
                                                };
                                                $rankTextColor = $entry['rank'] <= 3 ? 'text-white' : 'text-gray-700';
                                            @endphp
                                            <div class="flex items-center p-3 rounded-xl {{ $isTop3 ? 'bg-white shadow-lg border-2 border-yellow-300' : 'bg-white shadow border border-gray-200' }} hover:scale-102 transition-transform">
                                                <!-- Rank Badge -->
                                                <div class="flex-shrink-0 mr-3">
                                                    <div class="w-10 h-10 {{ $rankBg }} rounded-full flex items-center justify-center {{ $isTop3 ? 'shadow-md' : '' }}">
                                                        <span class="font-bold {{ $rankTextColor }}">{{ $entry['rank'] }}</span>
                                                    </div>
                                                </div>

                                                <!-- User Info -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center">
                                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold mr-2 text-sm">
                                                            {{ strtoupper(substr($entry['user']->name, 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <div class="font-semibold text-gray-900 text-sm truncate">
                                                                {{ $entry['user']->name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ $entry['score'] }}/{{ $entry['total_marks'] }} poin
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Score -->
                                                <div class="flex-shrink-0 text-right ml-3">
                                                    <div class="font-bold text-lg {{ $entry['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ number_format($entry['percentage'], 0) }}%
                                                    </div>
                                                    @if($entry['rank'] <= 3)
                                                        <div class="text-xs font-medium {{ $entry['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $entry['passed'] ? '✓ Lulus' : '✗ Tidak Lulus' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{ route('quizzes.leaderboard', $quiz) }}"
                                           class="inline-flex items-center text-sm font-medium text-amber-700 hover:text-amber-800">
                                            Lihat Semua Peringkat
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div class="w-20 h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                        </div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Belum Ada Peserta</h4>
                                        <p class="text-sm text-gray-600">Jadilah yang pertama di leaderboard!</p>
                                    </div>
                                @endif

                                <div class="mt-4 p-3 bg-gradient-to-r from-amber-100 to-yellow-100 rounded-lg border border-amber-200">
                                    <p class="text-xs text-center text-amber-800 font-medium">
                                        💪 Raih posisi teratas dan buktikan kemampuanmu!
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .prose {
            color: #374151;
        }

        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            color: #111827;
            font-weight: 600;
        }

        .prose p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .prose ul, .prose ol {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }

        .prose li {
            margin: 0.5rem 0;
        }

        .prose blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1rem;
            font-style: italic;
            color: #6b7280;
        }

        .prose code {
            background-color: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .prose a {
            color: #3b82f6;
            text-decoration: underline;
        }

        .prose a:hover {
            color: #1d4ed8;
        }

        .hover\:scale-102:hover {
            transform: scale(1.02);
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #fbbf24, #f59e0b);
            border-radius: 10px;
            border: 2px solid #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #f59e0b, #d97706);
        }

        /* For Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #fbbf24 #f1f5f9;
        }

        /* Smooth transitions */
        * {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
    </style>

    <script>
        // Add interactive feedback
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats cards on load
            const statCards = document.querySelectorAll('.grid > div');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
            });
        });
    </script>
</x-app-layout>
