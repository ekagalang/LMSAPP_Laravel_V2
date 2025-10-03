<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <div class="max-w-4xl mx-auto px-6 py-8">
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
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-6 py-8">
            <div class="grid lg:grid-cols-3 gap-8">
                
                <!-- Quiz Info Card -->
                <div class="lg:col-span-2">
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

                        <!-- Card Body -->
                        <div class="p-6">
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
                </div>

                <!-- Quiz Stats Sidebar -->
                <div class="space-y-6">
                    <!-- Stats Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 text-white">
                            <h3 class="font-bold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Detail Kuis
                            </h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <!-- Total Questions -->
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Pertanyaan</span>
                                </div>
                                <span class="font-bold text-lg text-blue-600">{{ $quiz->questions->count() }}</span>
                            </div>

                            <!-- Total Points -->
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Total Poin</span>
                                </div>
                                <span class="font-bold text-lg text-purple-600">{{ $quiz->total_marks }}</span>
                            </div>

                            <!-- Pass Marks -->
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Poin Lulus</span>
                                </div>
                                <span class="font-bold text-lg text-green-600">{{ $quiz->pass_marks }}</span>
                            </div>

                            <!-- Time Limit -->
                            @if($quiz->time_limit)
                                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Batas Waktu</span>
                                    </div>
                                    <span class="font-bold text-lg text-orange-600">{{ $quiz->time_limit }} menit</span>
                                </div>
                            @else
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Batas Waktu</span>
                                    </div>
                                    <span class="font-bold text-lg text-gray-600">Tidak ada</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Leaderboard Preview -->
                    @if($quiz->enable_leaderboard)
                        @php
                            $leaderboard = $quiz->getLeaderboardWithBestAttempts(3);
                        @endphp

                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl shadow-lg border-2 border-yellow-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Top 3
                                </h3>
                                <a href="{{ route('quizzes.leaderboard', $quiz) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    Lihat Semua →
                                </a>
                            </div>

                            @if($leaderboard->count() > 0)
                                <div class="space-y-2 mb-4">
                                    @foreach($leaderboard as $entry)
                                        @php
                                            $medalColors = ['text-yellow-500', 'text-gray-400', 'text-amber-600'];
                                            $medalColor = $medalColors[$entry['rank'] - 1] ?? 'text-gray-400';
                                        @endphp
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-white">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-bold text-sm {{ $medalColor }} w-6">
                                                    <i class="fas fa-medal"></i>
                                                </span>
                                                <div class="text-sm">
                                                    <div class="font-semibold text-gray-900">{{ Str::limit($entry['user']->name, 15) }}</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-sm {{ $entry['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($entry['percentage'], 0) }}%
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="text-gray-400 mb-2">
                                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500">Jadilah yang pertama!</p>
                                </div>
                            @endif

                            <div class="text-center mt-3">
                                <p class="text-xs text-gray-600">💪 Raih posisi teratas!</p>
                            </div>
                        </div>
                    @endif

                    <!-- Start Button -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
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

        /* Button hover effects */
        button:hover {
            transform: translateY(-1px);
        }
        
        /* Card animations */
        .bg-white {
            transition: all 0.3s ease;
        }
        
        .bg-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>

    <script>
        // Add some interactive feedback when hovering over stats
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.bg-blue-50, .bg-purple-50, .bg-green-50, .bg-orange-50, .bg-gray-50');
            
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</x-app-layout>