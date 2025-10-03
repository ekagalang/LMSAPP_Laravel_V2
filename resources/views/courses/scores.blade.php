<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900 leading-tight">Daftar Nilai Quiz</h2>
                        <p class="text-emerald-600 font-medium text-sm">{{ $course->title }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 max-w-2xl">Nilai quiz peserta dikelompokkan berdasarkan lesson. Tampilkan semua attempt termasuk re-attempt untuk quiz yang belum lulus.</p>
            </div>
            <a href="javascript:void(0)" onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-medium text-sm text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl shadow-sm" role="alert">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="course_filter" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                            </svg>
                            Pindah ke Kursus Lain
                        </label>
                        <select id="course_filter" onchange="if(this.value) window.location.href = this.value" class="w-full pl-4 pr-10 py-3 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 rounded-xl shadow-sm transition-all duration-200">
                            @foreach ($courseOptions as $opt)
                                <option value="{{ route('courses.scores', $opt) }}" @selected($opt->id == $course->id)>
                                    {{ $opt->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <form action="{{ route('courses.scores', $course) }}" method="GET" class="space-y-2">
                        <label for="search" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari Peserta di Kursus Ini
                        </label>
                        <div class="flex space-x-3">
                            <div class="relative flex-1">
                                <x-text-input type="text" name="search" id="search" value="{{ request('search') }}" class="w-full pl-10 rounded-xl" placeholder="Nama atau email..." />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-medium text-sm hover:from-emerald-600 hover:to-teal-700 shadow-lg hover:shadow-xl transition-all duration-200">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Course Statistics -->
            @if($lessonsWithQuizzes->count() > 0)
            <div class="mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl border border-emerald-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Kursus</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="text-2xl font-bold text-emerald-600">{{ $lessonsWithQuizzes->count() }}</div>
                        <div class="text-sm text-gray-600">Lesson dengan Quiz</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="text-2xl font-bold text-teal-600">{{ $lessonsWithQuizzes->sum(function($lesson) { return $lesson->contents->count(); }) }}</div>
                        <div class="text-sm text-gray-600">Total Quiz</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="text-2xl font-bold text-indigo-600">{{ $participantsData->count() }}</div>
                        <div class="text-sm text-gray-600">Peserta Aktif</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Participants Quiz Scores -->
            <div class="space-y-4">
                @forelse ($participantsData as $participant)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Participant Header - Clickable -->
                        <button type="button"
                                class="w-full bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 hover:from-gray-100 hover:to-gray-200 transition-colors duration-200"
                                onclick="toggleAccordion('participant-{{ $participant['id'] }}')">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-500 transition-transform duration-200 accordion-icon" id="icon-participant-{{ $participant['id'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-lg">
                                        {{ strtoupper(substr($participant['name'], 0, 2)) }}
                                    </div>
                                    <div class="text-left">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $participant['name'] }}</h3>
                                        <p class="text-sm text-gray-600">{{ $participant['email'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-emerald-600">{{ number_format($participant['overall_quiz_average'], 1) }}%</div>
                                        <div class="text-xs text-gray-500">Rata-rata Quiz</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-indigo-600">{{ $participant['total_quiz_attempts'] }}</div>
                                        <div class="text-xs text-gray-500">Total Attempt</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-gray-600">{{ $participant['progress_percentage'] }}%</div>
                                        <div class="text-xs text-gray-500">Progres</div>
                                    </div>
                                </div>
                            </div>
                        </button>

                        <!-- Quiz Details by Lesson - Collapsible -->
                        <div class="hidden p-6" id="content-participant-{{ $participant['id'] }}">
                            @if(count($participant['quiz_data']) > 0)
                                <div class="space-y-6">
                                    @foreach($participant['quiz_data'] as $lessonData)
                                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                                            <!-- Lesson Header -->
                                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="font-semibold text-gray-900">
                                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-800 text-xs font-bold rounded-full mr-2">
                                                            {{ $lessonData['lesson_order'] }}
                                                        </span>
                                                        {{ $lessonData['lesson_title'] }}
                                                    </h4>
                                                    <span class="text-sm text-gray-600">{{ count($lessonData['quizzes']) }} Quiz</span>
                                                </div>
                                            </div>

                                            <!-- Quizzes in this lesson -->
                                            <div class="divide-y divide-gray-100">
                                                @foreach($lessonData['quizzes'] as $quizData)
                                                    <div class="p-4">
                                                        <div class="flex items-start justify-between mb-3">
                                                            <div class="flex-1">
                                                                <h5 class="font-medium text-gray-900 mb-1">{{ $quizData['quiz_title'] }}</h5>
                                                                <div class="flex items-center space-x-4 text-sm">
                                                                    <span class="flex items-center">
                                                                        <span class="w-2 h-2 rounded-full mr-2 {{ $quizData['is_passed'] ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                                                        {{ $quizData['is_passed'] ? 'Lulus' : 'Belum Lulus' }}
                                                                    </span>
                                                                    <span class="text-gray-500">{{ $quizData['total_attempts'] }} attempt</span>
                                                                    @if($quizData['pass_marks'] > 0)
                                                                        <span class="text-gray-500">Min. {{ $quizData['pass_marks'] }} poin</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <div class="text-lg font-bold {{ $quizData['is_passed'] ? 'text-green-600' : 'text-red-600' }}">
                                                                    {{ number_format($quizData['latest_score'], 1) }}%
                                                                </div>
                                                                <div class="text-xs text-gray-500">Skor Terbaru</div>
                                                            </div>
                                                        </div>

                                                        @if($quizData['needs_retry'])
                                                            <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                                                <div class="flex items-center">
                                                                    <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                                    </svg>
                                                                    <span class="text-sm font-medium text-amber-800">Perlu mengulang quiz ini untuk lulus</span>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Attempts History -->
                                                        <div class="bg-gray-50 rounded-lg p-3">
                                                            <h6 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Riwayat Attempt</h6>
                                                            <div class="space-y-2">
                                                                @foreach($quizData['attempts'] as $attempt)
                                                                    <div class="flex items-center justify-between py-2 px-3 bg-white rounded-md {{ $attempt['is_latest'] ? 'ring-2 ring-emerald-500 ring-opacity-50' : '' }}">
                                                                        <div class="flex items-center space-x-3">
                                                                            <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-100 text-gray-700 text-xs font-bold rounded-full">
                                                                                {{ $attempt['attempt_number'] }}
                                                                            </span>
                                                                            @if($attempt['is_latest'])
                                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                                                                    Terbaru
                                                                                </span>
                                                                            @endif
                                                                            <span class="text-sm text-gray-900">
                                                                                {{ $attempt['score'] }}/{{ $attempt['total_marks'] }} poin
                                                                            </span>
                                                                        </div>
                                                                        <div class="flex items-center space-x-3">
                                                                            <span class="text-sm font-medium {{ $attempt['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                                                                {{ number_format($attempt['percentage'], 1) }}%
                                                                            </span>
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $attempt['passed'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                                {{ $attempt['passed'] ? 'Lulus' : 'Tidak Lulus' }}
                                                                            </span>
                                                                            <span class="text-xs text-gray-500">
                                                                                {{ $attempt['completed_at'] ? $attempt['completed_at']->format('d/m H:i') : '-' }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Attempt Quiz</h3>
                                    <p class="text-sm text-gray-500">Peserta ini belum mengerjakan quiz apapun.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Action Footer -->
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    Total {{ $participant['total_quiz_attempts'] }} attempt pada {{ count($participant['quiz_data']) }} lesson
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('courses.participant.progress', ['course' => $course, 'user' => $participant['id']]) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-5 font-medium rounded-lg text-emerald-700 bg-emerald-100 hover:bg-emerald-200 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Detail Progres
                                    </a>
                                    @can('grade quizzes')
                                        <a href="{{ route('courses.gradebook', $course) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-5 font-medium rounded-lg text-orange-700 bg-orange-100 hover:bg-orange-200 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                            </svg>
                                            Gradebook
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                        <svg class="w-20 h-20 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak ada peserta ditemukan</h3>
                        <p class="text-gray-500 mb-4">Tidak ada peserta yang cocok dengan kriteria pencarian atau belum ada peserta yang terdaftar di kursus ini.</p>
                        @if(request('search'))
                            <a href="{{ route('courses.scores', $course) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-xl font-medium text-sm hover:bg-emerald-700 transition-colors">
                                Tampilkan Semua Peserta
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function toggleAccordion(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(90deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-app-layout>