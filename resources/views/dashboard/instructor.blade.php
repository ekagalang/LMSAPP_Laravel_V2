<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Instruktur') }}
            </h2>
            <div class="text-sm text-gray-500">
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Selamat datang, {{ auth()->user()->name }}! 👨‍🏫</h3>
                    <p class="text-indigo-100">Kelola pembelajaran Anda dan pantau perkembangan peserta dengan mudah.</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- My Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Kursus Saya</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['courses']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex text-xs text-gray-600">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                    {{ $stats['courses']['published'] }} Published
                                </span>
                                <span class="flex items-center ml-3">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1"></span>
                                    {{ $stats['courses']['draft'] }} Draft
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Peserta</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['students']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                <span class="text-green-600 font-medium">+{{ $stats['students']['recent_enrollments'] }}</span>
                                pendaftaran baru (30 hari)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Kuis Aktif</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['quizzes']['total']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                {{ $stats['quizzes']['completed'] }} dari {{ $stats['quizzes']['attempts'] }} percobaan selesai
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Essays -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500 dashboard-card hover-lift">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Esai Menunggu</p>
                                <p class="text-2xl font-semibold text-gray-900 stat-number">{{ number_format($stats['essays']['pending']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-600">
                                dari {{ $stats['essays']['total'] }} total submisi
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Course Performance & Recent Activities -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Performa Kursus</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($stats['courses']['performance'] as $course)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover-lift">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $course['title'] }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($course['status']) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 mb-3">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $course['students'] }} peserta
                                        </div>
                                        <div class="w-full">
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>Rata-rata Progress</span>
                                                <span>{{ $course['progress'] }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-indigo-500 to-blue-500 h-2 rounded-full transition-all duration-300" style="width: {{ $course['progress'] }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('courses.show', $course['id']) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                                            Kelola
                                        </a>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <p class="mb-4">Belum ada kursus yang dibuat</p>
                                    <a href="{{ route('courses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                        Buat Kursus Pertama
                                    </a>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
                        </div>
                        <div class="p-6">
                            <!-- Tabs -->
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-8">
                                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm tab-button active" data-tab="students">
                                        Peserta Baru
                                    </button>
                                    <button class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm tab-button" data-tab="discussions">
                                        Diskusi
                                    </button>
                                </nav>
                            </div>

                            <!-- Tab Content -->
                            <div class="mt-4">
                                <!-- Students Tab -->
                                <div id="students-tab" class="tab-content">
                                    <div class="space-y-4">
                                        @forelse($stats['recent_activities']['students'] as $student)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover-lift">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $student->email }} • Terdaftar di {{ $student->courses->count() }} kursus</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">{{ $student->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-8 text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <p>Belum ada peserta baru</p>
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
                                                    <p class="text-xs text-gray-500">oleh {{ $discussion->user->name }} • {{ $discussion->content->lesson->course->title }}</p>
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
                            @can('manage own courses')
                            <a href="{{ route('courses.create') }}" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Buat Kursus Baru
                            </a>
                            @endcan

                            @can('view courses')
                            <a href="{{ route('courses.index') }}" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors hover-lift">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Kelola Kursus
                            </a>
                            @endcan

                            @can('grade quizzes')
                            @if($stats['courses']['total'] > 0)
                            <div class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-500 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                Penilaian & Gradebook
                                <span class="ml-auto text-xs text-orange-600">{{ $stats['essays']['pending'] }} pending</span>
                            </div>
                            @endif
                            @endcan

                            @can('view progress reports')
                            @if($stats['courses']['total'] > 0)
                            <div class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-500 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Lihat Progress
                                <span class="ml-auto text-xs text-green-600">{{ $stats['students']['total'] }} students</span>
                            </div>
                            @endif
                            @endcan

                            @can('manage discussions')
                            <div class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-500 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Kelola Diskusi
                                <span class="ml-auto text-xs text-blue-600">{{ $stats['discussions']['recent'] }} new</span>
                            </div>
                            @endcan
                        </div>
                    </div>

                    <!-- Teaching Summary -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Ringkasan Mengajar</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Pelajaran</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['content']['lessons'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Konten</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['content']['contents'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Diskusi Aktif</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $stats['discussions']['total'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Diskusi Minggu Ini</span>
                                    <span class="text-sm font-medium text-green-600">+{{ $stats['discussions']['recent'] }}</span>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">Status Pengajaran</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 status-indicator">
                                            Aktif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Center -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Pemberitahuan</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @if($stats['essays']['pending'] > 0)
                                <div class="flex items-start p-3 bg-orange-50 rounded-lg">
                                    <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                        <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-orange-800">Esai Menunggu Penilaian</p>
                                        <p class="text-xs text-orange-600">{{ $stats['essays']['pending'] }} esai menunggu penilaian dari Anda</p>
                                    </div>
                                </div>
                                @endif

                                @if($stats['discussions']['recent'] > 0)
                                <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-800">Diskusi Baru</p>
                                        <p class="text-xs text-blue-600">{{ $stats['discussions']['recent'] }} diskusi baru dimulai minggu ini</p>
                                    </div>
                                </div>
                                @endif

                                @if($stats['essays']['pending'] == 0 && $stats['discussions']['recent'] == 0)
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm">Semua tugas telah selesai!</p>
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
                        btn.classList.remove('border-indigo-500', 'text-indigo-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });

                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });

                    // Add active class to clicked button
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('border-indigo-500', 'text-indigo-600');

                    // Show corresponding content
                    document.getElementById(targetTab + '-tab').classList.remove('hidden');
                });
            });

            // Set first tab as active by default
            if (tabButtons.length > 0) {
                tabButtons[0].classList.remove('border-transparent', 'text-gray-500');
                tabButtons[0].classList.add('border-indigo-500', 'text-indigo-600');
            }

            // Auto-refresh notification
            setTimeout(() => {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-indigo-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
                notification.innerHTML = '👨‍🏫 Dashboard instruktur berhasil dimuat';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }, 500);

            // Progress bars animation
            const progressBars = document.querySelectorAll('[style*="width:"]');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 300);
            });
        });
    </script>
    @endpush
</x-app-layout>
