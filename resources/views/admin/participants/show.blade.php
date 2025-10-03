<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900 leading-tight">Detail Peserta</h2>
                        <p class="text-blue-600 font-medium text-sm">{{ $user->name }}</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.participants.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-medium text-sm text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Personal Info -->
                <div class="lg:col-span-1">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-8 text-center">
                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <span class="text-3xl font-bold text-blue-600">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-1">{{ $user->name }}</h3>
                            <p class="text-blue-100 text-sm">{{ $user->email }}</p>
                        </div>

                        <div class="px-6 py-6 space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Gender</label>
                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    @if($user->gender)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->gender == 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                            {{ $user->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Tidak diisi</span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Lahir</label>
                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $user->date_of_birth ? $user->date_of_birth->format('d F Y') : '-' }}
                                    @if($user->date_of_birth)
                                        <span class="text-xs text-gray-500">({{ floor($user->date_of_birth->diffInYears(now())) }} tahun)</span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Institusi</label>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ $user->institution_name ?? '-' }}</p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pekerjaan</label>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ $user->occupation ?? '-' }}</p>
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Terdaftar Sejak</label>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ $user->created_at->format('d F Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Course Enrollment -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900">Kursus yang Diikuti</h3>
                            <p class="text-sm text-gray-600 mt-1">Total {{ $enrolledCourses->count() }} kursus</p>
                        </div>

                        <div class="p-6">
                            @forelse($enrolledCourses as $enrollment)
                                @php
                                    $course = $enrollment['course'];
                                    $progress = $enrollment['progress'];
                                @endphp
                                <div class="mb-6 last:mb-0 border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-semibold text-gray-900">{{ $course->title }}</h4>
                                            <span class="text-sm font-medium text-blue-600">{{ $progress['progress_percentage'] }}%</span>
                                        </div>
                                        <div class="mt-2 bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress['progress_percentage'] }}%"></div>
                                        </div>
                                    </div>

                                    <div class="px-4 py-3 bg-white">
                                        <div class="grid grid-cols-3 gap-4 text-center">
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Lessons</p>
                                                <p class="text-lg font-bold text-gray-900">{{ $progress['completed_lessons'] }}/{{ $progress['total_lessons'] }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Quiz</p>
                                                <p class="text-lg font-bold text-gray-900">{{ $progress['completed_quizzes'] }}/{{ $progress['total_quizzes'] }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Rata-rata</p>
                                                <p class="text-lg font-bold {{ $progress['average_quiz_score'] >= 70 ? 'text-green-600' : 'text-orange-600' }}">
                                                    {{ number_format($progress['average_quiz_score'], 1) }}%
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-3 flex justify-end">
                                            <a href="{{ route('courses.participant.progress', ['course' => $course, 'user' => $user]) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                Lihat Detail Progress
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Mengikuti Kursus</h3>
                                    <p class="text-sm text-gray-500">Peserta ini belum terdaftar di kursus apapun.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
