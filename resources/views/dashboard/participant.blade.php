<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Peserta') }}
            </h2>
            <div class="text-sm text-gray-500">
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-green-600 to-teal-600 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Selamat datang, {{ auth()->user()->name }}! 🎓</h3>
                    <p class="text-green-100">Lanjutkan perjalanan pembelajaran Anda dan raih tujuan yang telah ditetapkan.</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Enrolled Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Kursus Diikuti</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['courses']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex text-xs text-gray-600">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                    {{ $stats['courses']['completed'] }} Selesai
                                </span>
                                <span class="flex items-center ml-3">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                                    {{ $stats['courses']['in_progress'] }} Berlangsung
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overall Progress -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Progress Keseluruhan</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ $stats['courses']['overall_progress'] }}%</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-teal-500 h-2 rounded-full transition-all duration-300" style="width: {{ $stats['courses']['overall_progress'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Performance -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 2h.01M9 8h.01M9 16h.01M12 16h.01M16 16h.01M16 8h.01"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Kuis Lulus</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['quizzes']['passed']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                dari {{ $stats['quizzes']['completed'] }} kuis selesai
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Completed -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Konten Selesai</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['content']['completed_contents']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                dari {{ $stats['content']['total_contents'] }} total konten
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Course Progress & Next to Study -->
                <div class="lg:col-span-2">
                    <!-- My Courses -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Kursus Saya</h3>
                        </div>
                        <div class="p-6">
                            @forelse($stats['courses']['progress'] as $course)
                            <div class="mb-6 last:mb-0 p-4 bg-gray-50 rounded-lg hover-lift">
                                <div class="flex items-start space-x-4">
                                    @if($course['thumbnail'])
                                    <img src="{{ asset('storage/' . $course['thumbnail']) }}" alt="{{ $course['title'] }}" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                    @else
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="text-lg font-medium text-gray-900 truncate">{{ $course['title'] }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2 flex-shrink-0
                                                {{ $course['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                                                   ($course['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ $course['status'] === 'completed' ? 'Selesai' :
                                                   ($course['status'] === 'in_progress' ? 'Berlangsung' : 'Belum Dimulai') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($course['description'], 120) }}</p>
                                        <div class="flex items-center text-xs text-gray-500 mb-3">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Instruktur: {{ $course['instructors']->pluck('name')->join(', ') }}
                                        </div>
                                        <div class="mb-3">
                                            <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                                                <span>Progress: {{ $course['progress'] }}%</span>
                                                <span>{{ $course['completed_lessons'] }}/{{ $course['total_lessons'] }} pelajaran</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-green-500 to-teal-500 h-2 rounded-full transition-all duration-300" style="width: {{ $course['progress'] }}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end">
                                            <a href="{{ route('courses.show', $course['id']) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors">
                                                {{ $course['status'] === 'not_started' ? 'Mulai Belajar' : 'Lanjutkan' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Kursus</h3>
                                <p class="text-gray-600 mb-4">Anda belum terdaftar di kursus manapun.</p>
                                <a href="{{ route('welcome') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                                    Jelajahi Kursus
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($stats['recent_activities']['completions'] as $completion)
                                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $completion->content_title }}</p>
                                        <p class="text-xs text-gray-600">{{ $completion->lesson_title }} • {{ $completion->course_title }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($completion->created_at)->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>Belum ada aktivitas minggu ini</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Learning Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Statistik Belajar</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Pelajaran Selesai</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['content']['completed_lessons'] }}/{{ $stats['content']['total_lessons'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Konten Selesai</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['content']['completed_contents'] }}/{{ $stats['content']['total_contents'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Kuis Selesai</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['quizzes']['completed'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Esai Terkirim</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['essays']['submissions'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Diskusi Dimulai</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['discussions']['started'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next to Study -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Selanjutnya Dipelajari</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @forelse($stats['recent_activities']['next_contents'] as $content)
                                <div class="p-3 bg-blue-50 rounded-lg hover-lift">
                                    <h4 class="text-sm font-medium text-blue-900">{{ $content->title }}</h4>
                                    <p class="text-xs text-blue-600">{{ $content->lesson->title }} • {{ $content->lesson->course->title }}</p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($content->type) }}
                                        </span>
                                        <a href="{{ route('courses.show', $content->lesson->course->id) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Mulai →
                                        </a>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm">Semua konten telah selesai!</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('welcome') }}" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Jelajahi Kursus Baru
                            </a>

                            @if($stats['courses']['total'] > 0)
                            <div class="flex items-center w-full px-4 py-3 text-left text-sm font-medium bg-gradient-to-r from-green-50 to-teal-50 rounded-lg border border-green-200">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-green-800 font-medium">Progress Keseluruhan</p>
                                    <p class="text-xs text-green-600">{{ $stats['courses']['overall_progress'] }}% dari semua kursus</p>
                                </div>
                            </div>
                            @endif

                            @if($stats['essays']['submissions'] > $stats['essays']['graded'])
                            <div class="flex items-center w-full px-4 py-3 text-left text-sm font-medium bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                                <svg class="w-5 h-5 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-orange-800 font-medium">Esai Menunggu Penilaian</p>
                                    <p class="text-xs text-orange-600">{{ $stats['essays']['submissions'] - $stats['essays']['graded'] }} esai belum dinilai</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .dashboard-card {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-variant-numeric: tabular-nums;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Smooth animations for progress bars */
        .transition-all {
            transition: all 0.5s ease-in-out;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh notification
            setTimeout(() => {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm transform translate-x-full transition-transform duration-300';
                notification.innerHTML = '🎓 Dashboard peserta berhasil dimuat';
                document.body.appendChild(notification);

                // Show notification
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);

                // Hide notification
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }, 500);

            // Animate progress bars on load
            const progressBars = document.querySelectorAll('[style*="width:"]');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 300);
            });

            // Add smooth scroll to course cards
            const courseCards = document.querySelectorAll('.hover-lift');
            courseCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Add loading state
                    if (e.target.tagName === 'A') return; // Don't interfere with actual links

                    this.style.opacity = '0.7';
                    setTimeout(() => {
                        this.style.opacity = '1';
                    }, 200);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
