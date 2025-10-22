<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-6">
            <div class="flex items-center space-x-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        {{ $course->title }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Detail dan manajemen kursus</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="javascript:void(0)" onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-medium text-sm text-gray-700 hover:bg-gray-50 hover:shadow-lg transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                @can('view', $course)
                    <a href="{{ route('courses.discussions.index', $course) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium text-sm hover:from-blue-600 hover:to-blue-700 shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Diskusi
                    </a>
                @endcan
                @can('grade quizzes')
                    <a href="{{ route('courses.gradebook', $course) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl font-medium text-sm hover:from-orange-600 hover:to-orange-700 shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Penilaian Essay
                    </a>
                @endcan
                @can('view progress reports')
                    <a href="{{ route('courses.scores', $course) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl font-medium text-sm hover:from-indigo-600 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Nilai Quiz
                    </a>
                    <a href="{{ route('courses.progress', $course) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-medium text-sm hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Lihat Progres
                    </a>
                @endcan
                @can('update', $course)
                    <a href="{{ route('courses.tokens', $course) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-medium text-sm hover:from-red-600 hover:to-red-700 shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Token Kelas
                    </a>
                    <a href="{{ route('attendance.course-report', $course) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium text-sm hover:from-blue-600 hover:to-blue-700 shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Attendance
                    </a>
                    <a href="{{ route('courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl font-medium text-sm hover:from-purple-600 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Kursus
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl shadow-sm" role="alert">
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

            <div x-data="{ currentTab: 'lessons' }" class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Enhanced Tab Navigation -->
                <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button @click="currentTab = 'lessons'"
                                :class="{'border-indigo-500 text-indigo-600 bg-indigo-50': currentTab === 'lessons', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': currentTab !== 'lessons'}"
                                class="whitespace-nowrap py-4 px-4 border-b-2 font-semibold text-sm rounded-t-lg transition-all duration-200">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span>Pelajaran & Konten</span>
                            </div>
                        </button>
                        {{-- 🆕 NEW: Periods & Chat Tab --}}
                        <button @click="currentTab = 'periods'"
                                :class="{'border-indigo-500 text-indigo-600 bg-indigo-50': currentTab === 'periods', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': currentTab !== 'periods'}"
                                class="whitespace-nowrap py-4 px-4 border-b-2 font-semibold text-sm rounded-t-lg transition-all duration-200">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Kelas & Chat</span>
                            </div>
                        </button>

                        @can('update', $course)
                            <button @click="currentTab = 'managers'"
                                    :class="{'border-indigo-500 text-indigo-600 bg-indigo-50': currentTab === 'managers', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': currentTab !== 'managers'}"
                                    class="whitespace-nowrap py-4 px-4 border-b-2 font-semibold text-sm rounded-t-lg transition-all duration-200">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Instruktur</span>
                                </div>
                            </button>
                            <button @click="currentTab = 'event_organizers'"
                                    :class="{'border-indigo-500 text-indigo-600 bg-indigo-50': currentTab === 'event_organizers', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': currentTab !== 'event_organizers'}"
                                    class="whitespace-nowrap py-4 px-4 border-b-2 font-semibold text-sm rounded-t-lg transition-all duration-200">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span>Event Organizer</span>
                                </div>
                            </button>
                            <button @click="currentTab = 'participants'"
                                    :class="{'border-indigo-500 text-indigo-600 bg-indigo-50': currentTab === 'participants', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': currentTab !== 'participants'}"
                                    class="whitespace-nowrap py-4 px-4 border-b-2 font-semibold text-sm rounded-t-lg transition-all duration-200">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span>Peserta Kursus</span>
                                </div>
                            </button>
                        @endcan
                    </nav>
                </div>

                <!-- Lessons Tab -->
                <div x-show="currentTab === 'lessons'" class="p-8">
                    <div
                        x-data="{
                            lessons: {{ Js::from($course->lessons->sortBy('order')->values()) }},
                            activeAccordion: null,
                            moveUp(index) {
                                if (index === 0) return;
                                [this.lessons[index - 1], this.lessons[index]] = [this.lessons[index], this.lessons[index - 1]];
                                this.updateLessonOrderOnServer();
                            },
                            moveDown(index) {
                                if (index === this.lessons.length - 1) return;
                                [this.lessons[index], this.lessons[index + 1]] = [this.lessons[index + 1], this.lessons[index]];
                                this.updateLessonOrderOnServer();
                            },
                            updateLessonOrderOnServer() {
                                const orderedIds = this.lessons.map(lesson => lesson.id);
                                fetch('{{ route('lessons.update_order') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ lessons: orderedIds })
                                });
                            },
                            moveContentUp(lessonIndex, contentIndex) {
                                if (contentIndex === 0) return;
                                let contents = this.lessons[lessonIndex].contents;
                                [contents[contentIndex - 1], contents[contentIndex]] = [contents[contentIndex], contents[contentIndex - 1]];
                                this.updateContentOrderOnServer(lessonIndex);
                            },
                            moveContentDown(lessonIndex, contentIndex) {
                                let contents = this.lessons[lessonIndex].contents;
                                if (contentIndex === contents.length - 1) return;
                                [contents[contentIndex], contents[contentIndex + 1]] = [contents[contentIndex + 1], contents[contentIndex]];
                                this.updateContentOrderOnServer(lessonIndex);
                            },
                            updateContentOrderOnServer(lessonIndex) {
                                const orderedContentIds = this.lessons[lessonIndex].contents.map(content => content.id);
                                fetch('{{ route('contents.update_order') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ contents: orderedContentIds })
                                });
                            }
                        }">

                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">Daftar Pelajaran</h3>
                                <p class="text-gray-600 mt-1">Kelola urutan dan konten pelajaran</p>
                            </div>
                            @can('update', $course)
                                <a href="{{ route('courses.lessons.create', $course) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl font-semibold text-sm hover:from-indigo-600 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Pelajaran
                                </a>
                            @endcan
                        </div>

                        <div x-show="lessons.length === 0" class="text-center py-16">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Pelajaran</h4>
                            <p class="text-gray-500">Tambahkan pelajaran pertama untuk memulai kursus ini.</p>
                        </div>

                        <div class="space-y-6">
                            <template x-for="(lesson, index) in lessons" :key="lesson.id">
                                <div class="bg-gradient-to-r from-white to-gray-50 rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300"
                                    :class="{ 'opacity-50 pointer-events-none': !isLessonUnlocked(lesson, index) }">

                                    <div class="p-6 flex justify-between items-center">
                                        <div class="flex items-center flex-grow">
                                            @can('update', $course)
                                                <div class="flex flex-col mr-4 space-y-1">
                                                    <button @click="moveUp(index)" :disabled="index === 0"
                                                            :class="{'opacity-25 cursor-not-allowed': index === 0}"
                                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                        </svg>
                                                    </button>
                                                    <button @click="moveDown(index)" :disabled="index === lessons.length - 1"
                                                            :class="{'opacity-25 cursor-not-allowed': index === lessons.length - 1}"
                                                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endcan
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                                    <span class="font-bold text-white text-lg" x-text="index + 1"></span>
                                                </div>
                                                <div>
                                                    <template x-if="!isLessonUnlocked(lesson, index)">
                                                        <div class="flex items-center space-x-2 mb-1">
                                                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span class="text-xs text-amber-600 font-medium">Terkunci</span>
                                                        </div>
                                                    </template>
                                                    <h4 class="text-xl font-bold text-gray-900" x-text="lesson.title"></h4>
                                                    <p class="text-gray-600 text-sm mt-1" x-text="lesson.description || 'Tidak ada deskripsi.'"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 flex-shrink-0">
                                            @can('update', $course)
                                                <!-- Action Buttons -->
                                                <div class="flex items-center space-x-2">
                                                    <form :action="`/courses/{{$course->id}}/lessons/${lesson.id}/duplicate`" method="POST" onsubmit="return confirm('Yakin ingin duplikasi pelajaran ini?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Duplikat
                                                        </button>
                                                    </form>

                                                    <a :href="`/courses/{{$course->id}}/lessons/${lesson.id}/edit`" class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>

                                                    <a :href="`/lessons/${lesson.id}/contents/create`" class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-200 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                        Tambah Konten
                                                    </a>

                                                    <form :action="`/courses/{{$course->id}}/lessons/${lesson.id}`" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelajaran ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @endcan

                                            <!-- Expand Button -->
                                            <button @click="activeAccordion = (activeAccordion === lesson.id) ? null : lesson.id"
                                                    class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                                                <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{'rotate-180': activeAccordion === lesson.id}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Content List -->
                                    <div x-show="activeAccordion === lesson.id" x-collapse.duration.300ms class="border-t border-gray-200 bg-gray-50">
                                        <div class="p-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <h5 class="text-lg font-semibold text-gray-800">Daftar Konten</h5>
                                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium" x-text="`${lesson.contents.length} konten`"></span>
                                            </div>

                                            <div class="space-y-3">
                                                <template x-for="(content, contentIndex) in lesson.contents" :key="content.id">
                                                    <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-200 hover:shadow-md transition-all duration-200">
                                                        <div class="flex items-center space-x-4">
                                                            @can('update', $course)
                                                            <div class="flex flex-col space-y-1">
                                                                <button @click="moveContentUp(index, contentIndex)" :disabled="contentIndex === 0"
                                                                        :class="{'opacity-25 cursor-not-allowed': contentIndex === 0}"
                                                                        class="p-1 hover:bg-gray-100 rounded">
                                                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                                    </svg>
                                                                </button>
                                                                <button @click="moveContentDown(index, contentIndex)" :disabled="contentIndex === lesson.contents.length - 1"
                                                                        :class="{'opacity-25 cursor-not-allowed': contentIndex === lesson.contents.length - 1}"
                                                                        class="p-1 hover:bg-gray-100 rounded">
                                                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            @endcan

                                                            <!-- Content Type Icon -->
                                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                                                 :class="{
                                                                    'bg-blue-100': content.type === 'text',
                                                                    'bg-red-100': content.type === 'video',
                                                                    'bg-green-100': content.type === 'quiz',
                                                                    'bg-gray-100': !['text', 'video', 'quiz'].includes(content.type)
                                                                 }">
                                                                <svg class="w-5 h-5"
                                                                     :class="{
                                                                        'text-blue-600': content.type === 'text',
                                                                        'text-red-600': content.type === 'video',
                                                                        'text-green-600': content.type === 'quiz',
                                                                        'text-gray-600': !['text', 'video', 'quiz'].includes(content.type)
                                                                     }"
                                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path x-show="content.type === 'text'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                    <path x-show="content.type === 'video'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.55a1 1 0 011.45.89V16.11a1 1 0 01-1.45.89L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                    <path x-show="content.type === 'quiz'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    <path x-show="!['text', 'video', 'quiz'].includes(content.type)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>

                                                            <div>
                                                                <a :href="`/contents/${content.id}`" class="text-lg font-medium text-indigo-600 hover:text-indigo-800 hover:underline transition-colors" x-text="content.title"></a>
                                                                <div class="flex items-center space-x-2 mt-1">
                                                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                                                          :class="{
                                                                            'bg-blue-100 text-blue-700': content.type === 'text',
                                                                            'bg-red-100 text-red-700': content.type === 'video',
                                                                            'bg-green-100 text-green-700': content.type === 'quiz',
                                                                            'bg-gray-100 text-gray-700': !['text', 'video', 'quiz'].includes(content.type)
                                                                          }"
                                                                          x-text="content.type.charAt(0).toUpperCase() + content.type.slice(1)"></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @can('update', $course)
                                                        <div class="flex items-center space-x-2">
                                                            <form :action="`/lessons/${lesson.id}/contents/${content.id}/duplicate`" method="POST" onsubmit="return confirm('Yakin ingin duplikasi konten ini?');">
                                                                @csrf
                                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 transition-colors">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                    Duplikat
                                                                </button>
                                                            </form>

                                                            <a :href="`/lessons/${lesson.id}/contents/${content.id}/edit`" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                                Edit
                                                            </a>

                                                            <form :action="`/lessons/${lesson.id}/contents/${content.id}`" method="POST" onsubmit="return confirm('Yakin ingin menghapus konten ini?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 text-xs font-medium rounded-lg hover:bg-red-100 transition-colors">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                </template>

                                                <div x-show="lesson.contents.length === 0" class="text-center py-12">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <p class="text-gray-500 text-sm">Belum ada konten untuk pelajaran ini.</p>
                                                    @can('update', $course)
                                                        <a :href="`/lessons/${lesson.id}/contents/create`" class="inline-flex items-center mt-3 px-4 py-2 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-200 transition-colors">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            Tambah Konten Pertama
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 🆕 NEW: Periods & Chat Tab --}}
                <div x-show="currentTab === 'periods'" x-cloak class="p-8" 
                     x-data="periodManager({{ $course->id }}, @js($course->periods->toArray() ?? []))">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900">Kelas & Komunikasi Kursus</h3>
                        <p class="text-gray-600 mt-1">Kelola kelas kursus dan akses chat realtime</p>
                    </div>

                    @if($course->periods && $course->periods->count() > 0)
                        <!-- Search and Bulk Actions -->
                        <div class="mb-6 bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Search -->
                                <div class="flex-1 max-w-md">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input x-model="searchTerm" type="text" 
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                               placeholder="Cari kelas berdasarkan nama atau deskripsi...">
                                    </div>
                                </div>

                                <!-- Bulk Actions -->
                                @can('update', $course)
                                <div class="flex items-center space-x-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">Pilih Semua</span>
                                    </label>
                                    
                                    <button @click="deleteSelected()" x-show="selectedPeriods.length > 0"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 shadow-md transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span x-text="`Hapus (${selectedPeriods.length})`"></span>
                                    </button>
                                </div>
                                @endcan
                            </div>
                        </div>

                        <!-- Status Summary -->
                        <div class="mb-6 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Kelas Aktif: {{ $course->periods->where('status', 'active')->count() }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Mendatang: {{ $course->periods->where('status', 'upcoming')->count() }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                    <span class="text-sm text-gray-600">Selesai: {{ $course->periods->where('status', 'completed')->count() }}</span>
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                @can('update', $course)
                                    <a href="{{ route('course-periods.create', $course) }}"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-md transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Tambah Kelas
                                    </a>
                                @endcan

                                @if($course->hasActivePeriod())
                                    <a href="{{ route('chat.index') }}?course={{ $course->id }}"
                                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 shadow-md transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        Buka Chat
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Period Cards with Search Filtering -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <template x-for="period in filteredPeriods" :key="period.id">
                                <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300"
                                     :class="period.status === 'active' ? 'ring-2 ring-green-300 border-green-200' : ''">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            @can('update', $course)
                                                <input type="checkbox" :value="period.id" x-model="selectedPeriods"
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            @endcan
                                            <h4 class="text-lg font-bold text-gray-900" x-text="period.name"></h4>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-green-100 text-green-800': period.status === 'active',
                                                  'bg-blue-100 text-blue-800': period.status === 'upcoming',
                                                  'bg-gray-100 text-gray-800': period.status === 'completed',
                                                  'bg-red-100 text-red-800': period.status === 'cancelled'
                                              }"
                                              x-text="period.status === 'active' ? '🟢 Aktif' : period.status === 'upcoming' ? '🔵 Akan Datang' : period.status === 'completed' ? '✅ Selesai' : '❌ Dibatalkan'">
                                        </span>
                                    </div>

                                    <div class="space-y-3 mb-6">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <div class="font-medium" x-text="`${new Date(period.start_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})} - ${new Date(period.end_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}`"></div>
                                                <div class="text-xs text-gray-500" x-text="`${Math.ceil((new Date(period.end_date) - new Date(period.start_date)) / (1000 * 60 * 60 * 24))} hari`"></div>
                                            </div>
                                        </div>

                                        <template x-if="period.status === 'active'">
                                            <div class="flex items-center text-sm text-green-600">
                                                <svg class="w-4 h-4 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="font-medium" x-text="`${Math.max(0, Math.ceil((new Date(period.end_date) - new Date()) / (1000 * 60 * 60 * 24)))} hari tersisa`"></span>
                                            </div>
                                        </template>

                                        <template x-if="period.description">
                                            <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg" x-text="period.description.substring(0, 100) + (period.description.length > 100 ? '...' : '')"></div>
                                        </template>
                                    </div>

                                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                        <div>
                                            <template x-if="period.status === 'active'">
                                                <a :href="`{{ route('chat.index') }}?period=${period.id}`"
                                                   class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-800 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    Masuk Chat
                                                </a>
                                            </template>
                                            <template x-if="period.status === 'upcoming'">
                                                <span class="inline-flex items-center text-sm text-blue-600">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Belum dimulai
                                                </span>
                                            </template>
                                            <template x-if="period.status === 'completed' || period.status === 'cancelled'">
                                                <span class="inline-flex items-center text-sm text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Selesai
                                                </span>
                                            </template>
                                        </div>

                                        @can('update', $course)
                                            <div class="flex items-center space-x-2">
                                                <a :href="`{{ url('courses/' . $course->id . '/periods') }}/${period.id}/manage`"
                                                   class="text-xs text-green-600 hover:text-green-800 font-medium">Kelola</a>
                                                <a :href="`{{ url('courses/' . $course->id . '/periods') }}/${period.id}/edit`"
                                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                                <button @click="deletePeriod(period.id)" class="text-xs text-red-600 hover:text-red-800 font-medium">Hapus</button>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- No results message -->
                        <div x-show="filteredPeriods.length === 0 && searchTerm !== ''" class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada kelas yang ditemukan</h4>
                            <p class="text-gray-600" x-text="`Tidak ada kelas yang cocok dengan \"${searchTerm}\"`"></p>
                        </div>

                        @if($course->periods->where('status', 'active')->count() === 0)
                            <div class="mt-8 p-6 bg-yellow-50 border border-yellow-200 rounded-xl">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.232 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-lg font-medium text-yellow-800">Tidak ada kelas aktif</h4>
                                        <p class="text-sm text-yellow-700 mt-1">Chat tidak tersedia saat ini. Tambahkan kelas baru atau aktifkan kelas yang ada.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @else
                        {{-- No Periods State --}}
                        <div class="text-center py-16">
                            <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Kelas Kursus</h4>
                            <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">Buat kelas kursus untuk mengaktifkan fitur chat dan mengelola timeline pembelajaran.</p>
                            @can('update', $course)
                                <a href="{{ route('course-periods.create', ['course' => $course->id]) }}"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Buat Kelas Pertama
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>

                @can('update', $course)
                    <!-- Managers Tab -->
                    <div x-show="currentTab === 'managers'" x-cloak class="p-8">
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gray-900">Manajemen Instruktur</h3>
                            <p class="text-gray-600 mt-1">Kelola instruktur yang ditugaskan untuk kursus ini</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Current Instructors -->
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-6 border border-red-200">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-red-900">Instruktur Ditugaskan</h4>
                                        <p class="text-sm text-red-700">{{ $course->instructors->count() }} instruktur aktif</p>
                                    </div>
                                </div>

                                <form action="{{ route('courses.removeInstructor', $course) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus instruktur terpilih?');">
                                    @csrf @method('DELETE')
                                    <div class="space-y-3 mb-6 max-h-80 overflow-y-auto">
                                        @forelse($course->instructors as $instructor)
                                            <div class="flex items-center p-3 bg-white rounded-xl border border-red-200 hover:bg-red-50 transition-colors">
                                                <input type="checkbox" name="user_ids[]" value="{{ $instructor->id }}" id="instructor-{{$instructor->id}}" class="mr-3 rounded border-red-300 text-red-600 focus:ring-red-500">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-semibold">{{ strtoupper(substr($instructor->name, 0, 1)) }}</span>
                                                    </div>
                                                    <label for="instructor-{{$instructor->id}}" class="font-medium text-gray-900 cursor-pointer">{{ $instructor->name }}</label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-8">
                                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-red-600 font-medium">Belum ada instruktur ditugaskan</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    @if($course->instructors->isNotEmpty())
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus Instruktur Terpilih
                                        </button>
                                    @endif
                                </form>
                            </div>

                            <!-- Available Instructors -->
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-green-900">Tambahkan Instruktur</h4>
                                        <p class="text-sm text-green-700">{{ $availableInstructors->count() }} instruktur tersedia</p>
                                    </div>
                                </div>

                                <form action="{{ route('courses.addInstructor', $course) }}" method="POST">
                                    @csrf
                                    <div class="space-y-3 mb-6 max-h-80 overflow-y-auto">
                                        @forelse($availableInstructors as $instructor)
                                            <div class="flex items-center p-3 bg-white rounded-xl border border-green-200 hover:bg-green-50 transition-colors">
                                                <input type="checkbox" name="user_ids[]" value="{{ $instructor->id }}" id="avail-instructor-{{$instructor->id}}" class="mr-3 rounded border-green-300 text-green-600 focus:ring-green-500">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-semibold">{{ strtoupper(substr($instructor->name, 0, 1)) }}</span>
                                                    </div>
                                                    <label for="avail-instructor-{{$instructor->id}}" class="font-medium text-gray-900 cursor-pointer">{{ $instructor->name }}</label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-8">
                                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-green-600 font-medium">Semua instruktur sudah ditugaskan</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    {{-- All pagination removed - now using Collection directly --}}
                                    @if($availableInstructors->count() > 0)
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tambahkan Instruktur
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Event Organizers Tab -->
                    <div x-show="currentTab === 'event_organizers'" x-cloak class="p-8">
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gray-900">Manajemen Event Organizer</h3>
                            <p class="text-gray-600 mt-1">Kelola event organizer yang ditugaskan untuk kursus ini</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Current Event Organizers -->
                            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-6 border border-purple-200">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-purple-900">EO Ditugaskan</h4>
                                        <p class="text-sm text-purple-700">{{ $course->eventOrganizers->count() }} EO aktif</p>
                                    </div>
                                </div>

                                <form action="{{ route('courses.removeEo', $course) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus EO terpilih?');">
                                    @csrf @method('DELETE')
                                    <div class="space-y-3 mb-6 max-h-80 overflow-y-auto">
                                        @forelse($course->eventOrganizers as $organizer)
                                            <div class="flex items-center p-3 bg-white rounded-xl border border-purple-200 hover:bg-purple-50 transition-colors">
                                                <input type="checkbox" name="user_ids[]" value="{{ $organizer->id }}" id="organizer-{{$organizer->id}}" class="mr-3 rounded border-purple-300 text-purple-600 focus:ring-purple-500">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-semibold">{{ strtoupper(substr($organizer->name, 0, 1)) }}</span>
                                                    </div>
                                                    <label for="organizer-{{$organizer->id}}" class="font-medium text-gray-900 cursor-pointer">{{ $organizer->name }}</label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-8">
                                                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-purple-600 font-medium">Belum ada EO ditugaskan</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    @if($course->eventOrganizers->isNotEmpty())
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus EO Terpilih
                                        </button>
                                    @endif
                                </form>
                            </div>

                            <!-- Available Event Organizers -->
                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border border-blue-200">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-blue-900">Tambahkan EO</h4>
                                        <p class="text-sm text-blue-700">{{ $availableOrganizers->count() }} EO tersedia</p>
                                    </div>
                                </div>

                                <form action="{{ route('courses.addEo', $course) }}" method="POST">
                                    @csrf
                                    <div class="space-y-3 mb-6 max-h-80 overflow-y-auto">
                                        @forelse($availableOrganizers as $organizer)
                                            <div class="flex items-center p-3 bg-white rounded-xl border border-blue-200 hover:bg-blue-50 transition-colors">
                                                <input type="checkbox" name="user_ids[]" value="{{ $organizer->id }}" id="avail-organizer-{{$organizer->id}}" class="mr-3 rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-semibold">{{ strtoupper(substr($organizer->name, 0, 1)) }}</span>
                                                    </div>
                                                    <label for="avail-organizer-{{$organizer->id}}" class="font-medium text-gray-900 cursor-pointer">{{ $organizer->name }}</label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-8">
                                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-blue-600 font-medium">Semua EO sudah ditugaskan</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    {{-- All pagination removed - now using Collection directly --}}
                                    @if($availableOrganizers->count() > 0)
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tambahkan EO
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Participants Tab -->
                    <div x-show="currentTab === 'participants'" x-cloak class="p-8"
                         x-data="{
                            selectedEnrollUsers: [],
                            selectedUnenrollUsers: [],
                            searchTermEnroll: '',
                            searchTermUnenroll: '',
                            unEnrolledParticipantsData: {{ Js::from($unEnrolledParticipants) }},
                            enrolledParticipantsData: {{ Js::from($course->enrolledUsers) }},
                            get filteredUnEnrolledParticipants() {
                                if (this.searchTermEnroll === '') return this.unEnrolledParticipantsData;
                                return this.unEnrolledParticipantsData.filter(user =>
                                    user.name.toLowerCase().includes(this.searchTermEnroll.toLowerCase()) ||
                                    user.email.toLowerCase().includes(this.searchTermEnroll.toLowerCase())
                                );
                            },
                            get filteredEnrolledParticipants() {
                                if (this.searchTermUnenroll === '') return this.enrolledParticipantsData;
                                return this.enrolledParticipantsData.filter(user =>
                                    user.name.toLowerCase().includes(this.searchTermUnenroll.toLowerCase()) ||
                                    user.email.toLowerCase().includes(this.searchTermUnenroll.toLowerCase())
                                );
                            }
                        }">
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gray-900">Manajemen Peserta Kursus</h3>
                            <p class="text-gray-600 mt-1">Kelola pendaftaran dan akses peserta kursus</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Enrolled Participants -->
                            <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-6 border border-orange-200">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-orange-900">Peserta Terdaftar</h4>
                                        <p class="text-sm text-orange-700">{{ $course->enrolledUsers->count() }} peserta aktif</p>
                                    </div>
                                </div>

                                @if($course->enrolledUsers->isEmpty())
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-orange-600 font-medium">Belum ada peserta terdaftar</p>
                                    </div>
                                @else
                                    <form id="unenroll-form" method="POST" action="{{ route('courses.unenroll_mass', $course) }}" onsubmit="return confirm('Anda yakin ingin mencabut akses peserta terpilih?');">
                                        @csrf @method('DELETE')

                                        <!-- Search Input -->
                                        <div class="mb-4">
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                                <input type="text" x-model="searchTermUnenroll" placeholder="Cari peserta terdaftar..." class="block w-full pl-10 pr-3 py-2 border border-orange-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                                            </div>
                                        </div>

                                        <!-- Participants List -->
                                        <div class="space-y-3 mb-6 max-h-80 overflow-y-auto">
                                            <template x-for="participant in filteredEnrolledParticipants" :key="participant.id">
                                                <div class="flex items-center p-3 bg-white rounded-xl border border-orange-200 hover:bg-orange-50 transition-colors">
                                                    <input type="checkbox" name="user_ids[]" :value="participant.id" x-model="selectedUnenrollUsers" class="mr-3 rounded border-orange-300 text-orange-600 focus:ring-orange-500">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center">
                                                            <span class="text-white text-sm font-semibold" x-text="participant.name.charAt(0).toUpperCase()"></span>
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-gray-900" x-text="participant.name"></p>
                                                            <p class="text-sm text-gray-600" x-text="participant.email"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="filteredEnrolledParticipants.length === 0">
                                                <div class="text-center py-8">
                                                    <p class="text-orange-600 font-medium">Tidak ada hasil pencarian</p>
                                                </div>
                                            </template>
                                        </div>

                                        <button type="submit" x-bind:disabled="selectedUnenrollUsers.length === 0"
                                                :class="selectedUnenrollUsers.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                                class="w-full inline-flex justify-center items-center px-4 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Cabut Akses Terpilih
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Available Participants -->
                            <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 border border-emerald-200">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-emerald-900">Daftarkan Peserta</h4>
                                        <p class="text-sm text-emerald-700">{{ $unEnrolledParticipants->count() }} calon peserta tersedia</p>
                                    </div>
                                </div>

                                <form id="enroll-form" method="POST" action="{{ route('courses.enroll', $course) }}">
                                    @csrf

                                    <!-- Search Input -->
                                    <div class="mb-4">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>
                                            <input type="text" x-model="searchTermEnroll" placeholder="Cari calon peserta..." class="block w-full pl-10 pr-3 py-2 border border-emerald-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                    </div>

                                    <!-- Available Users List -->
                                    <div class="space-y-3 mb-6 max-h-80 overflow-y-auto">
                                        <template x-for="user in filteredUnEnrolledParticipants" :key="user.id">
                                            <div class="flex items-center p-3 bg-white rounded-xl border border-emerald-200 hover:bg-emerald-50 transition-colors">
                                                <input type="checkbox" name="user_ids[]" :value="user.id" x-model="selectedEnrollUsers" class="mr-3 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-semibold" x-text="user.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-900" x-text="user.name"></p>
                                                        <p class="text-sm text-gray-600" x-text="user.email"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="filteredUnEnrolledParticipants.length === 0">
                                            <div class="text-center py-8">
                                                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-emerald-600 font-medium">Semua pengguna sudah terdaftar atau tidak ada hasil</p>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- REMOVED: Pagination links --}}

                                    <button type="submit" x-bind:disabled="selectedEnrollUsers.length === 0"
                                            :class="selectedEnrollUsers.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                            class="w-full inline-flex justify-center items-center px-4 py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        Daftarkan Terpilih
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <script>
        function periodManager(courseId, initialPeriods) {
            return {
                selectedPeriods: [],
                searchTerm: '',
                selectAll: false,
                periods: initialPeriods || [],
                
                get filteredPeriods() {
                    if (!this.searchTerm) return this.periods;
                    return this.periods.filter(period => 
                        period.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                        (period.description && period.description.toLowerCase().includes(this.searchTerm.toLowerCase()))
                    );
                },
                
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedPeriods = this.filteredPeriods.map(p => p.id);
                    } else {
                        this.selectedPeriods = [];
                    }
                },
                
                deleteSelected() {
                    if (this.selectedPeriods.length === 0) {
                        alert('Pilih kelas yang ingin dihapus');
                        return;
                    }
                    
                    if (confirm(`Yakin ingin menghapus ${this.selectedPeriods.length} kelas yang dipilih?`)) {
                        // Create forms and submit them
                        this.selectedPeriods.forEach(periodId => {
                            this.deletePeriod(periodId);
                        });
                    }
                },
                
                deletePeriod(periodId) {
                    if (confirm('Yakin ingin menghapus kelas ini?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/courses/${courseId}/periods/${periodId}`;
                        
                        // Add CSRF token
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = '{{ csrf_token() }}';
                        form.appendChild(tokenInput);
                        
                        // Add method override
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            }
        }

        function isLessonUnlocked(lesson, index) {
            // Simple unlock logic - you can modify this based on your requirements
            // For now, lessons are unlocked sequentially
            return true; // or implement your unlock logic here
        }
    </script>
</x-app-layout>
