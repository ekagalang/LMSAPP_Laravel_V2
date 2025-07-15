<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Admin') }}
            </h2>
            <div class="text-sm text-gray-500">
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Selamat datang, {{ auth()->user()->name }}! 👋</h3>
                    <p class="text-blue-100">Kelola sistem pembelajaran dengan mudah melalui dashboard komprehensif ini.</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['users']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex text-xs text-gray-600">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                    {{ $stats['users']['participants'] }} Peserta
                                </span>
                                <span class="flex items-center ml-3">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                                    {{ $stats['users']['instructors'] }} Instruktur
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Courses -->
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
                                <p class="text-sm font-medium text-gray-500">Kursus Aktif</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['courses']['published']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex text-xs text-gray-600">
                                <span>{{ $stats['courses']['draft'] }} Draft</span>
                                <span class="mx-2">•</span>
                                <span>{{ $stats['courses']['recent_enrollments'] }} Pendaftaran Baru (30 hari)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discussions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Diskusi</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['discussions']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                <span class="text-green-600 font-medium">+{{ $stats['discussions']['recent'] }}</span>
                                diskusi baru minggu ini
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Statistics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Kuis Selesai</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['quizzes']['completed']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                dari {{ $stats['quizzes']['attempts'] }} total percobaan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Activities -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
                        </div>
                        <div class="p-6">
                            <!-- Tabs -->
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-8">
                                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm tab-button active" data-tab="courses">
                                        Kursus Baru
                                    </button>
                                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm tab-button" data-tab="users">
                                        Pengguna Baru
                                    </button>
                                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm tab-button" data-tab="discussions">
                                        Diskusi
                                    </button>
                                </nav>
                            </div>

                            <!-- Tab Content -->
                            <div class="mt-4">
                                <!-- Courses Tab -->
                                <div id="courses-tab" class="tab-content">
                                    <div class="space-y-4">
                                        @forelse($stats['recent_activities']['courses'] as $course)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover-lift">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                                                    <p class="text-xs text-gray-500">oleh {{ $course->instructors->first()->name ?? 'Unknown' }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($course->status) }}
                                                </span>
                                                <p class="text-xs text-gray-500 mt-1">{{ $course->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-8 text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <p>Belum ada kursus yang dibuat</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Users Tab -->
                                <div id="users-tab" class="tab-content hidden">
                                    <div class="space-y-4">
                                        @forelse($stats['recent_activities']['users'] as $user)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover-lift">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $user->getRoleNames()->first() ?? 'participant' }}
                                                </span>
                                                <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-8 text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <p>Belum ada pengguna baru</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Discussions Tab -->
                                <div id="discussions-tab" class="tab-content hidden">
                                    <div class="space-y-4">
                                        @forelse($stats['recent_activities']['discussions'] as $discussion)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover-lift">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ Str::limit($discussion->title, 40) }}</p>
                                                    <p class="text-xs text-gray-500">oleh {{ $discussion->user->name }} • {{ $discussion->content->lesson->course->title ?? 'Unknown Course' }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">{{ $discussion->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-8 text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            <p>Belum ada diskusi</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Summary -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('admin.users.index') }}" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                Kelola Pengguna
                            </a>
                            <a href="{{ route('courses.index') }}" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Lihat Semua Kursus
                            </a>
                            <a href="{{ route('admin.roles.index') }}" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                Kelola Peran
                            </a>
                        </div>
                    </div>

                    <!-- System Summary -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Ringkasan Sistem</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Essay Submissions</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['essays']['submissions'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Essays Graded</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['essays']['graded'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Event Organizers</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['users']['event_organizers'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Kursus</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['courses']['total'] }}</span>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">System Health</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 status-indicator">
                                            Excellent
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Enrollments Chart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Tren Pendaftaran</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($stats['trends']['monthly_enrollments'] as $month)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">
                                        {{ DateTime::createFromFormat('!m', $month->month)->format('M') }} {{ $month->year }}
                                    </span>
                                    <div class="flex items-center">
                                        <div class="w-20 h-2 bg-gray-200 rounded-full mr-2">
                                            <div class="h-2 bg-blue-500 rounded-full" style="width: {{ min(100, ($month->count / max($stats['trends']['monthly_enrollments']->max('count'), 1)) * 100) }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $month->count }}</span>
                                    </div>
                                </div>
                                @endforeach

                                @if($stats['trends']['monthly_enrollments']->isEmpty())
                                <div class="text-center py-4 text-gray-500">
                                    <p class="text-sm">Belum ada data pendaftaran</p>
                                </div>
                                @endif
                            </div>
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

        .tab-button {
            transition: all 0.3s ease;
        }

        .tab-content {
            transition: opacity 0.3s ease;
            opacity: 1;
        }

        .status-indicator.online::before {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-blue-500', 'text-blue-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });

                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });

                    // Add active class to clicked button
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('border-blue-500', 'text-blue-600');

                    // Show corresponding content
                    document.getElementById(targetTab + '-tab').classList.remove('hidden');
                });
            });

            // Set first tab as active by default
            if (tabButtons.length > 0) {
                tabButtons[0].classList.remove('border-transparent', 'text-gray-500');
                tabButtons[0].classList.add('border-blue-500', 'text-blue-600');
            }

            // Auto-refresh notification
            setTimeout(() => {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
                notification.innerHTML = '✓ Dashboard loaded successfully';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }, 500);
        });
    </script>
    @endpush
</x-app-layout>
