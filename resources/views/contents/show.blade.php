<x-app-layout>
    <div x-data="{
        sidebarOpen: window.innerWidth >= 768,
        showProgress: false,
        // [LOGIKA BARU] Menentukan apakah konten ini dianggap selesai.
        // Untuk kuis, harus lulus. Untuk esai, sudah submit (bukan harus dinilai).
        @php
            $user = Auth::user();
            $isTask = in_array($content->type, ['quiz', 'essay']);
            $isContentEffectivelyCompleted = false;

            if ($content->type === 'quiz' && $content->quiz_id) {
                // Dianggap selesai jika ada percobaan yang lulus
                $isContentEffectivelyCompleted = $user->quizAttempts()->where('quiz_id', $content->quiz_id)->where('passed', true)->exists();
            } elseif ($content->type === 'essay') {
                // ✅ PERUBAHAN: Dianggap selesai jika sudah ada submission (tidak perlu dinilai)
                $isContentEffectivelyCompleted = $user->essaySubmissions()->where('content_id', $content->id)->exists();
            } else {
                // Untuk konten biasa, cek di tabel pivot
                $isContentEffectivelyCompleted = $user->completedContents->contains($content->id);
            }
        @endphp
        contentCompleted: {{ $isContentEffectivelyCompleted ? 'true' : 'false' }},

        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen },

        // Fungsi ini akan men-submit form untuk menandai selesai
        markAsCompleted() {
            // Hanya submit jika bukan tugas (kuis/esai)
            @if(!$isTask)
                document.getElementById('complete-form').submit();
            @endif
        }
    }"
    class="flex flex-col lg:flex-row min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">

        <!-- [BARU] Form tersembunyi untuk menandai selesai (hanya untuk konten non-tugas) -->
        @if(!$isTask)
        <form id="complete-form" action="{{ route('contents.complete_and_continue', $content->id) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif

        <!-- Mobile Header -->
        <div class="lg:hidden bg-white shadow-sm border-b p-4 flex items-center justify-between sticky top-0 z-40">
            <button @click="toggleSidebar()" class="p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex-1 mx-4">
                <h1 class="text-lg font-bold text-gray-900 truncate">{{ $content->title }}</h1>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium capitalize">
                        {{ ucfirst($content->type) }}
                    </span>
                </div>
            </div>
            <button @click="showProgress = !showProgress" class="p-2 rounded-xl bg-purple-100 text-purple-600 hover:bg-purple-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <aside
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed lg:static inset-y-0 left-0 w-full sm:w-96 bg-white shadow-2xl lg:shadow-xl border-r border-gray-200 flex-shrink-0 z-50 lg:z-20 flex flex-col">

            <!-- Sidebar Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold truncate">{{ $course->title }}</h3>
                        <p class="text-indigo-100 text-sm mt-1">Pembelajaran Interaktif</p>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                @php
                    $totalContentsInCourse = $course->lessons->flatMap->contents->count();
                    // ✅ PERBAIKAN: Use fresh query for completed count dengan logika essay yang baru
                    $completedContentsCount = 0;
                    foreach ($course->lessons as $lesson) {
                        foreach ($lesson->contents as $contentItem) {
                            if ($contentItem->type === 'quiz' && $contentItem->quiz_id) {
                                if ($user->quizAttempts()->where('quiz_id', $contentItem->quiz_id)->where('passed', true)->exists()) {
                                    $completedContentsCount++;
                                }
                            } elseif ($contentItem->type === 'essay') {
                                // ✅ PERUBAHAN: Essay dianggap selesai jika sudah submit
                                if ($user->essaySubmissions()->where('content_id', $contentItem->id)->exists()) {
                                    $completedContentsCount++;
                                }
                            } else {
                                if ($user->completedContents()->where('content_id', $contentItem->id)->exists()) {
                                    $completedContentsCount++;
                                }
                            }
                        }
                    }
                    $progressPercentage = $totalContentsInCourse > 0 ? round(($completedContentsCount / $totalContentsInCourse) * 100) : 0;
                @endphp
                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span>Progress</span>
                        <span class="font-semibold">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-gradient-to-r from-yellow-400 to-green-400 h-2 rounded-full transition-all duration-500"
                             style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <p class="text-xs text-indigo-100 mt-1">{{ $completedContentsCount }}/{{ $totalContentsInCourse }} konten selesai</p>
                </div>
            </div>

            <!-- Course Navigation -->
            <nav class="flex-1 overflow-y-auto p-6">
                @foreach ($course->lessons->sortBy('order') as $lesson)
                    <div class="mb-6">
                        <!-- Lesson Header -->
                        <div class="flex items-center mb-3 pb-2 border-b border-gray-100">
                            <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                {{ $loop->iteration }}
                            </div>
                            <h4 class="font-semibold text-gray-900 flex-1">{{ $lesson->title }}</h4>
                            @php
                                $lessonContentsCount = $lesson->contents->count();
                                // ✅ PERBAIKAN: Calculate lesson completed count dengan logika essay yang konsisten
                                $lessonCompletedCount = 0;
                                foreach ($lesson->contents as $contentItem) {
                                    if ($contentItem->type === 'quiz' && $contentItem->quiz_id) {
                                        if ($user->quizAttempts()->where('quiz_id', $contentItem->quiz_id)->where('passed', true)->exists()) {
                                            $lessonCompletedCount++;
                                        }
                                    } elseif ($contentItem->type === 'essay') {
                                        // ✅ PERUBAHAN: Essay dianggap selesai jika sudah submit
                                        if ($user->essaySubmissions()->where('content_id', $contentItem->id)->exists()) {
                                            $lessonCompletedCount++;
                                        }
                                    } else {
                                        if ($user->completedContents()->where('content_id', $contentItem->id)->exists()) {
                                            $lessonCompletedCount++;
                                        }
                                    }
                                }
                            @endphp
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                {{ $lessonCompletedCount }}/{{ $lessonContentsCount }}
                            </span>
                        </div>

                        <!-- Content List -->
                        <ul class="space-y-2">
                            @foreach ($lesson->contents->sortBy('order') as $c)
                                @php
                                    $cIsTask = in_array($c->type, ['quiz', 'essay']);

                                    // ✅ PERBAIKAN: Consistent completion check dengan logika essay baru
                                    if ($c->type === 'quiz' && $c->quiz_id) {
                                        $isCompleted = $user->quizAttempts()->where('quiz_id', $c->quiz_id)->where('passed', true)->exists();
                                    } elseif ($c->type === 'essay') {
                                        // ✅ PERUBAHAN: Essay dianggap selesai jika sudah submit
                                        $isCompleted = $user->essaySubmissions()->where('content_id', $c->id)->exists();
                                    } else {
                                        // ✅ PERBAIKAN: Use fresh query instead of loaded relation
                                        $isCompleted = $user->completedContents()->where('content_id', $c->id)->exists();
                                    }

                                    $isCurrent = $c->id === $content->id;
                                    $isUnlocked = $unlockedContents->contains('id', $c->id);
                                @endphp
                                <li>
                                    @if($isUnlocked)
                                        <a href="{{ route('contents.show', $c) }}"
                                           class="group block p-3 rounded-xl transition-all duration-200 hover:shadow-md
                                                  {{ $isCurrent
                                                      ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg'
                                                      : ($isCompleted
                                                          ? 'bg-green-50 hover:bg-green-100 text-green-800'
                                                          : 'bg-gray-50 hover:bg-gray-100 text-gray-700') }}">

                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 flex-shrink-0
                                                            {{ $isCurrent ? 'bg-white/20 text-white' : ($isCompleted ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-600') }}">
                                                    @switch($c->type)
                                                        @case('video') 🎥 @break @case('document') 📄 @break @case('image') 🖼️ @break
                                                        @case('quiz') 🧠 @break @case('essay') ✍️ @break @default 📝
                                                    @endswitch
                                                </div>
                                                <div class="flex-1 min-w-0"><p class="font-medium truncate">{{ $c->title }}</p><p class="text-xs opacity-75 capitalize">{{ ucfirst($c->type) }}</p></div>
                                                <div class="ml-2 flex-shrink-0">
                                                    @if($isCurrent)<div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 8.207a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L11 9.586z" clip-rule="evenodd"/></svg></div>
                                                    @elseif($isCompleted)<div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></div>
                                                    @else<div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center"><div class="w-2 h-2 bg-gray-500 rounded-full"></div></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <div class="group block p-3 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 bg-gray-200 text-gray-500"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg></div>
                                                <div class="flex-1 min-w-0"><p class="font-medium truncate">{{ $c->title }}</p><p class="text-xs opacity-75 capitalize">{{ ucfirst($c->type) }}</p></div>
                                                <div class="ml-2 flex-shrink-0"><div class="w-6 h-6 bg-gray-300 rounded-full"></div></div>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            <div class="p-6 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-medium rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-h-screen lg:min-h-0">
            <header class="hidden lg:flex items-center justify-between p-6 bg-white border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <button @click="toggleSidebar()" class="p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $content->title }}</h1>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium capitalize">{{ ucfirst($content->type) }}</span>
                            @if($content->description)<span class="text-gray-400">•</span><span class="text-sm text-gray-600">{{ Str::limit($content->description, 50) }}</span>@endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div x-show="!contentCompleted">
                        @if(!$isTask)<button @click="markAsCompleted()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-all duration-200 hover:scale-105 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm">Tandai Selesai</span>
                        </button>
                        @endif
                    </div>
                    <div x-show="contentCompleted">
                        @php
                            $user = Auth::user();
                            $contentStatus = $user->getContentStatus($content);
                            $statusText = $user->getContentStatusText($content);
                            $badgeClass = $user->getContentStatusBadgeClass($content);
                        @endphp

                        <div class="inline-flex items-center px-4 py-2 {{ $badgeClass }} font-medium rounded-lg">
                            @if($contentStatus === 'completed')
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($contentStatus === 'pending_grade')
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($contentStatus === 'failed')
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="text-sm">{{ $statusText }}</span>
                        </div>

                        {{-- Additional Info for Essay Content --}}
                        @if($content->type === 'essay' && $contentStatus === 'pending_grade')
                            @php
                                $submission = $user->essaySubmissions()->where('content_id', $content->id)->first();
                                $totalQuestions = $content->essayQuestions()->count();
                                $gradedAnswers = $submission ? $submission->answers()->whereNotNull('score')->count() : 0;
                            @endphp
                            <div class="mt-2 text-sm text-yellow-700 bg-yellow-50 p-2 rounded">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Progres Penilaian: {{ $gradedAnswers }}/{{ $totalQuestions }} pertanyaan sudah dinilai
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            <!-- ✅ PERBAIKAN: Content Container dengan padding bottom yang cukup untuk bottom bar -->
            <div class="flex-1 overflow-y-auto pb-32">
                <div class="max-w-4xl mx-auto p-6 lg:p-8">
                    <!-- Content Card -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                        <!-- Content Header (Mobile) -->
                        <div class="lg:hidden bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                            <h2 class="text-xl font-bold mb-2">{{ $content->title }}</h2>
                            @if($content->description)
                                <p class="text-indigo-100">{{ $content->description }}</p>
                            @endif
                        </div>

                        <!-- Content Body -->
                        <div class="p-6 lg:p-8">
                            @if($content->description)
                                <div class="mb-6 pb-6 border-b border-gray-100">
                                    <p class="text-lg text-gray-600 leading-relaxed">{{ $content->description }}</p>
                                </div>
                            @endif

                            <!-- Content Display Based on Type -->
                            <div class="content-display">
                                @if($content->type == 'video')
                                    <div class="prose max-w-none text-gray-700 leading-relaxed mb-8">
                                        {!! $content->description !!}
                                    </div>

                                    {{-- Coba tampilkan video yang disematkan --}}
                                    <div class="aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black">
                                        <iframe
                                            class="w-full h-full"
                                            src="{{ $content->youtube_embed_url }}"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                            {{-- Tambahkan penanganan error sederhana jika iframe gagal dimuat --}}
                                            onerror="this.style.display='none'; document.getElementById('youtube-fallback').style.display='block';"
                                        ></iframe>
                                    </div>

                                    {{-- Tampilan Fallback jika video tidak bisa disematkan --}}
                                    <div id="youtube-fallback" style="display:none;" class="mt-4">
                                        <p class="text-center text-yellow-600 bg-yellow-100 p-4 rounded-lg">
                                            Video tidak dapat diputar di sini.
                                        </p>
                                        <a href="{{ $content->body }}" target="_blank" rel="noopener noreferrer" class="block group mt-2">
                                            <div class="relative rounded-2xl overflow-hidden shadow-lg">
                                                <img src="{{ $content->youtube_thumbnail_url }}" alt="Video thumbnail" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center group-hover:bg-opacity-60 transition-all duration-300">
                                                    <div class="text-center text-white">
                                                        <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path></svg>
                                                        <p class="font-bold text-xl mt-2">Tonton di YouTube</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                @elseif($content->type == 'image' && $content->file_path)
                                    <div class="text-center">
                                        <div class="inline-block rounded-2xl overflow-hidden shadow-2xl">
                                            <img src="{{ Storage::url($content->file_path) }}"
                                                 alt="{{ $content->title }}"
                                                 class="max-w-full h-auto max-h-96 object-contain">
                                        </div>
                                        <p class="text-sm text-gray-500 mt-4">{{ $content->title }}</p>
                                    </div>

                                @elseif($content->type == 'document' && $content->file_path)
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 text-center border border-blue-100">
                                        <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Dokumen Pembelajaran</h3>
                                        <p class="text-gray-600 mb-6">{{ basename($content->file_path) }}</p>
                                        <a href="{{ Storage::url($content->file_path) }}"
                                           target="_blank"
                                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Download Dokumen
                                        </a>
                                    </div>

                                @elseif($content->type == 'text')
                                    <div class="prose prose-lg max-w-none">
                                        <div class="content-text text-gray-800 leading-relaxed">
                                            {!! $content->body !!}
                                        </div>
                                    </div>

                                @elseif($content->type == 'essay')
                                    {{-- Essay Content Body --}}
                                    @if($content->body)
                                        <div class="prose prose-lg max-w-none mb-8">
                                            <div class="content-text text-gray-800 leading-relaxed">
                                                {!! $content->body !!}
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Essay Questions Section --}}
                                    @php
                                        $submission = $content->essaySubmissions()->where('user_id', Auth::id())->first();
                                        $questions = $content->essayQuestions;
                                    @endphp

                                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-100">
                                        {{-- JIKA SUDAH ADA JAWABAN --}}
                                        @if ($submission)
                                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 rounded-lg">
                                                <div class="flex items-center mb-4">
                                                    <svg class="w-8 h-8 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <div>
                                                        <p class="font-bold text-lg">Anda Sudah Mengumpulkan Jawaban</p>
                                                        <p class="text-sm">Dikumpulkan pada: {{ $submission->created_at->format('d F Y, H:i') }}</p>
                                                    </div>
                                                </div>

                                                @if ($submission->is_fully_graded)
                                                    <div class="mt-4 flex space-x-4">
                                                        <a href="{{ route('essays.result', $submission->id) }}" 
                                                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            Lihat Nilai dan Feedback
                                                        </a>
                                                        <div class="flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg">
                                                            <span class="font-medium">Total Nilai: {{ $submission->total_score }}/{{ $submission->max_total_score }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center mt-4 p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                                                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>Jawaban Anda sedang menunggu penilaian dari instruktur.</span>
                                                    </div>
                                                @endif
                                            </div>

                                        {{-- JIKA BELUM ADA JAWABAN DAN USER ADALAH PESERTA --}}
                                        @elseif (Auth::user()->hasRole('participant'))
                                            @if ($questions->isEmpty())
                                                {{-- Fallback untuk essay lama tanpa questions --}}
                                                <div class="text-center mb-6">
                                                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Essay Assignment</h3>
                                                    <p class="text-gray-600">Tulis jawaban essay Anda di bawah ini</p>
                                                </div>

                                                <form action="{{ route('essays.store', $content) }}" method="POST">
                                                    @csrf
                                                    <div class="mb-6">
                                                        <label for="essay_editor" class="block text-sm font-medium text-gray-700 mb-2">
                                                            Tulis Jawaban Anda:
                                                        </label>
                                                        <textarea id="essay_editor" name="essay_content" rows="10" 
                                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-vertical"
                                                                placeholder="Tulis jawaban essay Anda di sini..."
                                                                required></textarea>
                                                    </div>
                                                    
                                                    <div class="text-center">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                                                                onclick="return confirm('Apakah Anda yakin ingin mengumpulkan essay ini? Anda tidak dapat mengubah jawaban setelah dikumpulkan.')">
                                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                            </svg>
                                                            Kirim Jawaban
                                                        </button>
                                                    </div>
                                                </form>
                                            @else
                                                {{-- NEW SYSTEM: Multiple questions --}}
                                                <div class="text-center mb-6">
                                                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Essay Assignment</h3>
                                                    <p class="text-gray-600">Jawab {{ $questions->count() }} pertanyaan essay di bawah ini</p>
                                                </div>

                                                <form action="{{ route('essays.store', $content) }}" method="POST" class="space-y-8">
                                                    @csrf
                                                    
                                                    @foreach ($questions->sortBy('order') as $index => $question)
                                                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                                            <div class="flex justify-between items-start mb-4">
                                                                <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                                                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                                                        {{ $index + 1 }}
                                                                    </span>
                                                                    Pertanyaan {{ $index + 1 }}
                                                                </h4>
                                                                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                                                    {{ $question->max_score }} poin
                                                                </span>
                                                            </div>
                                                            
                                                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                                                <p class="text-gray-800 leading-relaxed">{{ $question->question }}</p>
                                                            </div>
                                                            
                                                            <div class="space-y-2">
                                                                <label for="answer_{{ $question->id }}" class="block text-sm font-medium text-gray-700">
                                                                    Jawaban Anda:
                                                                </label>
                                                                <textarea
                                                                    id="answer_{{ $question->id }}"
                                                                    name="answer_{{ $question->id }}"
                                                                    rows="6"
                                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-vertical"
                                                                    placeholder="Tulis jawaban Anda untuk pertanyaan {{ $index + 1 }}..."
                                                                    required>{{ old("answer_{$question->id}") }}</textarea>
                                                                @error("answer_{$question->id}")
                                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                                        <p class="text-sm text-gray-600">
                                                            Total: {{ $questions->sum('max_score') }} poin
                                                        </p>
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                                                                onclick="return confirm('Apakah Anda yakin ingin mengumpulkan semua jawaban essay ini? Anda tidak dapat mengubah jawaban setelah dikumpulkan.')">
                                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                            </svg>
                                                            Kirim Semua Jawaban
                                                        </button>
                                                    </div>
                                                </form>
                                            @endif

                                        {{-- JIKA USER BISA EDIT CONTENT (instructor/admin) --}}
                                        @elseif (Auth::user()->can('update', $content->lesson->course))
                                            <div class="text-center mb-6">
                                                <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Kelola Pertanyaan Essay</h3>
                                                <p class="text-gray-600">Tambah dan kelola pertanyaan untuk essay ini</p>
                                            </div>
                                            
                                            {{-- Form tambah pertanyaan --}}
                                            <form action="{{ route('essay.questions.store', $content) }}" method="POST" class="mb-8 p-6 bg-white rounded-xl border border-gray-200">
                                                @csrf
                                                <h4 class="font-semibold text-gray-900 mb-4">Tambah Pertanyaan Baru</h4>
                                                
                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                                                            Pertanyaan:
                                                        </label>
                                                        <textarea
                                                            id="question"
                                                            name="question"
                                                            rows="3"
                                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                            placeholder="Masukkan pertanyaan essay..."
                                                            required></textarea>
                                                    </div>
                                                    
                                                    <div>
                                                        <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">
                                                            Skor Maksimal:
                                                        </label>
                                                        <input
                                                            type="number"
                                                            id="max_score"
                                                            name="max_score"
                                                            min="1"
                                                            max="1000"
                                                            value="100"
                                                            class="w-32 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                            required>
                                                    </div>
                                                    
                                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                        </svg>
                                                        Tambah Pertanyaan
                                                    </button>
                                                </div>
                                            </form>

                                            {{-- List pertanyaan existing --}}
                                            @if ($questions->count() > 0)
                                                <div class="space-y-4">
                                                    <h4 class="font-semibold text-gray-900">Pertanyaan yang Ada ({{ $questions->count() }})</h4>
                                                    @foreach ($questions->sortBy('order') as $index => $question)
                                                        <div class="flex items-start justify-between p-6 bg-white border border-gray-200 rounded-lg">
                                                            <div class="flex-1">
                                                                <div class="flex items-center mb-2">
                                                                    <span class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                                                        {{ $index + 1 }}
                                                                    </span>
                                                                    <h5 class="font-medium text-gray-900">Soal {{ $index + 1 }}</h5>
                                                                    <span class="ml-auto px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">{{ $question->max_score }} poin</span>
                                                                </div>
                                                                <p class="text-gray-600 ml-9">{{ Str::limit($question->question, 150) }}</p>
                                                            </div>
                                                            <form action="{{ route('essay.questions.destroy', $question->id) }}" method="POST" class="ml-4">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                                        onclick="return confirm('Yakin ingin menghapus pertanyaan ini?')"
                                                                        title="Hapus pertanyaan">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endforeach
                                                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                                                        <p class="text-sm text-blue-700 font-medium">
                                                            Total skor maksimal: {{ $questions->sum('max_score') }} poin
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center p-8 bg-gray-50 rounded-lg">
                                                    <div class="w-12 h-12 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <p class="text-gray-600">Belum ada pertanyaan. Tambahkan pertanyaan pertama untuk essay ini.</p>
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                @elseif($content->type == 'quiz' && $content->quiz)
                                    <!-- ✅ PERBAIKAN: Tampilkan quiz content dengan benar -->
                                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-8 border border-purple-100">
                                        <div class="text-center mb-6">
                                            <div class="w-20 h-20 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $content->quiz->title }}</h3>
                                            @if($content->quiz->description)
                                                <p class="text-gray-600 mb-4">{{ $content->quiz->description }}</p>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                            <div class="bg-white p-4 rounded-xl text-center">
                                                <div class="text-2xl font-bold text-purple-600">{{ $content->quiz->questions->count() }}</div>
                                                <div class="text-sm text-gray-600">Pertanyaan</div>
                                            </div>
                                            <div class="bg-white p-4 rounded-xl text-center">
                                                <div class="text-2xl font-bold text-purple-600">{{ $content->quiz->total_marks }}</div>
                                                <div class="text-sm text-gray-600">Total Poin</div>
                                            </div>
                                            <div class="bg-white p-4 rounded-xl text-center">
                                                <div class="text-2xl font-bold text-purple-600">
                                                    @if($content->quiz->time_limit)
                                                        {{ $content->quiz->time_limit }} min
                                                    @else
                                                        Tanpa Batas
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-600">Waktu</div>
                                            </div>
                                        </div>

                                        @php
                                            $userAttempts = Auth::user()->quizAttempts()->where('quiz_id', $content->quiz->id)->get();
                                            $bestAttempt = $userAttempts->sortByDesc('score')->first();
                                            $hasPassedAttempt = $userAttempts->where('passed', true)->isNotEmpty();
                                        @endphp

                                        @if($userAttempts->isNotEmpty())
                                            <div class="bg-white rounded-xl p-6 mb-6">
                                                <h4 class="font-semibold text-gray-900 mb-4">Riwayat Percobaan</h4>
                                                <div class="space-y-3">
                                                    @foreach($userAttempts->sortByDesc('completed_at') as $attempt)
                                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                            <div>
                                                                <span class="text-sm text-gray-600">
                                                                    {{ $attempt->completed_at ? $attempt->completed_at->format('d M Y, H:i') : 'Sedang berlangsung' }}
                                                                </span>
                                                                @if($attempt->passed)
                                                                    <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">LULUS</span>
                                                                @endif
                                                            </div>
                                                            @if($attempt->completed_at)
                                                                <div class="text-right">
                                                                    <div class="font-semibold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                                                        {{ $attempt->score }}/{{ $content->quiz->total_marks }}
                                                                    </div>
                                                                    <a href="{{ route('quizzes.result', [$content->quiz, $attempt]) }}"
                                                                       class="text-xs text-indigo-600 hover:text-indigo-800">Lihat Detail</a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <div class="text-center">
                                            {{-- Gunakan variabel $hasPassedQuizBefore dari controller --}}
                                            @if($hasPassedQuizBefore)
                                                {{-- Jika sudah pernah lulus, tampilkan pesan sukses dan link ke hasil --}}
                                                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-xl flex items-center justify-center">
                                                    <svg class="w-6 h-6 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>Selamat! Anda telah lulus kuis ini.</span>
                                                </div>
                                                @php
                                                    $lastPassedAttempt = $userAttempts->where('passed', true)->sortByDesc('completed_at')->first();
                                                @endphp
                                                @if($lastPassedAttempt)
                                                    <a href="{{ route('quizzes.result', ['quiz' => $content->quiz, 'attempt' => $lastPassedAttempt]) }}"
                                                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                        Lihat Hasil Kelulusan
                                                    </a>
                                                @endif
                                            @else
                                                {{-- Jika belum pernah lulus, tampilkan tombol Mulai/Coba Lagi --}}
                                                <a href="{{ route('quizzes.start', $content->quiz) }}"
                                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $userAttempts->isNotEmpty() ? 'Coba Lagi' : 'Mulai Kuis' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                @elseif($content->type == 'zoom')
                                    @php
                                        $zoomDetails = json_decode($content->body, true);
                                        $schedulingStatus = $content->getSchedulingStatus();
                                    @endphp
                                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-8 border border-blue-100">
                                        <div class="text-center mb-6">
                                            <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.55a1 1 0 011.45.89V16.11a1 1 0 01-1.45.89L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Rapat Online via Zoom</h3>
                                            <p class="text-gray-600 mb-4">
                                                @if($content->is_scheduled)
                                                    Meeting dijadwalkan {{ $content->getScheduledStartInTimezone()->format('d M Y, H:i') }} - {{ $content->getScheduledEndInTimezone()->format('H:i') }} WIB
                                                @else
                                                    Gunakan detail di bawah ini untuk bergabung ke dalam rapat.
                                                @endif
                                            </p>
                                        </div>

                                        {{-- ✅ Scheduling Status Display --}}
                                        @if($content->is_scheduled)
                                            <div class="mb-6">
                                                @if($schedulingStatus['status'] === 'upcoming')
                                                    <div class="bg-yellow-100 border border-yellow-300 rounded-xl p-4 text-center">
                                                        <div class="flex items-center justify-center mb-2">
                                                            <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span class="font-semibold text-yellow-800">Meeting Belum Dimulai</span>
                                                        </div>
                                                        <p class="text-yellow-700">{{ $schedulingStatus['message'] }}</p>
                                                        <p class="text-sm text-yellow-600 mt-1">{{ $schedulingStatus['starts_in'] }}</p>
                                                    </div>
                                                @elseif($schedulingStatus['status'] === 'active')
                                                    <div class="bg-green-100 border border-green-300 rounded-xl p-4 text-center">
                                                        <div class="flex items-center justify-center mb-2">
                                                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span class="font-semibold text-green-800">Meeting Sedang Berlangsung</span>
                                                        </div>
                                                        <p class="text-green-700">{{ $schedulingStatus['message'] }}</p>
                                                        <p class="text-sm text-green-600 mt-1">Berakhir {{ $schedulingStatus['ends_in'] }}</p>
                                                    </div>
                                                @elseif($schedulingStatus['status'] === 'ended')
                                                    <div class="bg-red-100 border border-red-300 rounded-xl p-4 text-center">
                                                        <div class="flex items-center justify-center mb-2">
                                                            <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span class="font-semibold text-red-800">Meeting Telah Berakhir</span>
                                                        </div>
                                                        <p class="text-red-700">{{ $schedulingStatus['message'] }}</p>
                                                        <p class="text-sm text-red-600 mt-1">Berakhir {{ $schedulingStatus['ended_ago'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="bg-white rounded-xl p-6 mb-6 divide-y divide-gray-200">
                                            <div class="flex items-center py-3">
                                                <span class="font-semibold w-32 text-gray-600">Link Rapat</span>
                                                @if($schedulingStatus['can_join'])
                                                    <a href="{{ $zoomDetails['link'] ?? '#' }}" target="_blank" class="text-blue-600 hover:underline break-all">
                                                        {{ $zoomDetails['link'] ?? 'Tidak tersedia' }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 break-all">{{ $zoomDetails['link'] ?? 'Tidak tersedia' }}</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center py-3">
                                                <span class="font-semibold w-32 text-gray-600">Meeting ID</span>
                                                <span class="text-gray-800 font-medium">{{ $zoomDetails['meeting_id'] ?? 'Tidak tersedia' }}</span>
                                            </div>
                                            @if(!empty($zoomDetails['password']))
                                            <div class="flex items-center py-3">
                                                <span class="font-semibold w-32 text-gray-600">Password</span>
                                                <span class="text-gray-800 font-medium">{{ $zoomDetails['password'] }}</span>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="text-center">
                                            @if($schedulingStatus['can_join'])
                                                <a href="{{ $zoomDetails['link'] ?? '#' }}" target="_blank" 
                                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                    </svg>
                                                    Gabung Sekarang
                                                </a>
                                            @else
                                                <button disabled 
                                                        class="inline-flex items-center px-8 py-4 bg-gray-400 text-white font-bold rounded-xl cursor-not-allowed opacity-50">
                                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H9m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Meeting Belum Tersedia
                                                </button>
                                                <p class="text-sm text-gray-500 mt-2">
                                                    @if($schedulingStatus['status'] === 'upcoming')
                                                        Meeting akan dibuka otomatis saat waktu yang dijadwalkan
                                                    @else
                                                        Meeting sudah tidak tersedia
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Discussion Section -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6 text-white">
                            <h3 class="text-xl font-bold flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.304L3 21l1.304-5.094A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                                </svg>
                                Diskusi & Tanya Jawab
                            </h3>
                            <p class="text-purple-100 mt-1">Berbagi pemikiran dan bertanya tentang materi ini</p>
                        </div>
                        <div class="p-6 lg:p-8">
                            @include('contents.partials.discussion-section')
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- ✅ PERBAIKAN UTAMA: Bottom Navigation dengan positioning yang lebih robust -->
        <div class="fixed bottom-0 bg-white/98 backdrop-blur-md border-t border-gray-200 shadow-2xl z-[9999] transition-all duration-300 ease-in-out"
             :style="{
                'left': sidebarOpen && window.innerWidth >= 1024 ? '384px' : '0px',
                'right': '0px'
             }">
            @php
                // ✅ PERBAIKAN: Mendapatkan konten dalam urutan yang benar
                $allContents = $orderedContents; // Gunakan data yang sudah diurutkan dari controller
                $currentIndex = $allContents->search(function($item) use ($content) {
                    return $item->id === $content->id;
                });

                $previousContent = $currentIndex > 0 ? $allContents->get($currentIndex - 1) : null;
                $nextContent = ($currentIndex !== false && $currentIndex < $allContents->count() - 1) ? $allContents->get($currentIndex + 1) : null;

                // ✅ PERBAIKAN LOGIC: Untuk quiz, cek apakah sudah lulus. Untuk essay, cek apakah sudah submit.
                if ($content->type === 'quiz' && $content->quiz_id) {
                    $canGoNext = $user->quizAttempts()
                        ->where('quiz_id', $content->quiz_id)
                        ->where('passed', true)
                        ->exists() && $nextContent;
                } elseif ($content->type === 'essay') {
                    // ✅ PERUBAHAN: Essay bisa lanjut setelah submit
                    $canGoNext = $user->essaySubmissions()
                        ->where('content_id', $content->id)
                        ->exists() && $nextContent;
                } else {
                    $canGoNext = $isContentEffectivelyCompleted && $nextContent;
                }

                // ✅ FITUR BARU: Cek apakah ini konten terakhir dan semua sudah selesai
                $isLastContent = !$nextContent && $isContentEffectivelyCompleted;
                $allCourseContents = $course->lessons->flatMap->contents;
                $isAllCourseCompleted = true;

                if ($isLastContent) {
                    foreach ($allCourseContents as $courseContent) {
                        $isContentDone = false;

                        if ($courseContent->type === 'quiz' && $courseContent->quiz_id) {
                            $isContentDone = $user->quizAttempts()
                                ->where('quiz_id', $courseContent->quiz_id)
                                ->where('passed', true)
                                ->exists();
                        } elseif ($courseContent->type === 'essay') {
                            $isContentDone = $user->essaySubmissions()
                                ->where('content_id', $courseContent->id)
                                ->exists();
                        } else {
                            $isContentDone = $user->completedContents()
                                ->where('content_id', $courseContent->id)
                                ->exists();
                        }

                        if (!$isContentDone) {
                            $isAllCourseCompleted = false;
                            break;
                        }
                    }
                }
            @endphp

            <!-- Mobile Bottom Navigation -->
            <div class="lg:hidden">
                <div class="px-4 py-3">
                    <div class="flex items-center space-x-3">
                        @if ($previousContent)
                            <a href="{{ route('contents.show', $previousContent) }}"
                               class="flex-shrink-0 p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-200 hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>
                        @endif

                        <div class="flex-1">
                            @if ($canGoNext)
                                <a href="{{ route('contents.show', $nextContent) }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg group">
                                    <span class="text-sm mr-2">Selanjutnya</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @elseif(!$nextContent && $isContentEffectivelyCompleted)
                                <!-- ✅ FITUR BARU: Cek apakah semua kursus sudah selesai -->
                                @if($isAllCourseCompleted)
                                    <form action="{{ route('contents.complete_and_continue', $content->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                               class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                            <span class="text-sm mr-2">🎉 Selesaikan Kursus</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('courses.show', $course->id) }}"
                                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                        <span class="text-sm mr-2">Kembali ke Kursus</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </a>
                                @endif
                            @elseif(!$isContentEffectivelyCompleted && !$isTask)
                                <button @click="markAsCompleted()"
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md transition-all duration-200 hover:scale-105">
                                    Tandai Selesai
                                </button>
                            @else
                                <!-- ✅ TAMBAHAN: Pesan untuk quiz yang belum diselesaikan -->
                                <div class="w-full text-center py-3">
                                    <p class="text-sm text-gray-600">
                                        @if($content->type === 'quiz')
                                            Selesaikan quiz untuk melanjutkan
                                        @elseif($content->type === 'essay')
                                            Submit essay untuk melanjutkan
                                        @else
                                            Selesaikan materi ini untuk melanjutkan
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>

                        
                    </div>
                </div>
            </div>

            <!-- Desktop Bottom Navigation -->
            <div class="hidden lg:block">
                <div class="px-6 py-3">
                    <div class="max-w-6xl mx-auto flex items-center justify-center space-x-6">
                        <div>
                            @if ($previousContent)
                                <a href="{{ route('contents.show', $previousContent) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all duration-200 group max-w-sm hover:scale-105">
                                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    <div class="text-left">
                                        <div class="text-xs text-gray-500">Sebelumnya</div>
                                        <div class="text-sm font-semibold truncate">{{ Str::limit($previousContent->title, 30) }}</div>
                                    </div>
                                </a>
                            @endif
                        </div>

                        <div>
                            @if ($canGoNext)
                                <a href="{{ route('contents.show', $nextContent) }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                    <span class="mr-2">Selanjutnya</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @elseif (!$nextContent && $isContentEffectivelyCompleted)
                                {{-- ✅ FIX: Simple completion button - let controller handle the logic --}}
                                <form action="{{ route('contents.complete_and_continue', $content->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                        <span class="mr-2">✅</span>
                                        Selesai & Lanjutkan
                                    </button>
                                </form>
                            @elseif (!$isContentEffectivelyCompleted && !$isTask)
                                <button @click="markAsCompleted()"
                                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 hover:scale-105">
                                    Tandai Selesai untuk Lanjut
                                </button>
                            @else
                                {{-- ✅ TAMBAHAN: Pesan untuk desktop --}}
                                <div class="text-center py-3">
                                    <p class="text-sm text-gray-600">
                                        @if($content->type === 'quiz')
                                            Selesaikan quiz untuk melanjutkan
                                        @elseif($content->type === 'essay')
                                            Submit essay untuk melanjutkan
                                        @else
                                            Selesaikan tugas ini untuk melanjutkan
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Mobile Progress Modal -->
        <div x-show="showProgress"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[10000] flex items-center justify-center p-4"
             @click="showProgress = false">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl" @click.stop>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Progress Pembelajaran</h3>
                    <p class="text-gray-600 text-sm">{{ $course->title }}</p>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total Progress</span>
                        <span class="text-lg font-bold text-indigo-600">{{ $progressPercentage }}%</span>
                    </div>

                    <div class="relative">
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-4 rounded-full transition-all duration-1000 relative overflow-hidden"
                                 style="width: {{ $progressPercentage }}%">
                                <div class="absolute inset-0 bg-white/20 rounded-full animate-pulse"></div>
                            </div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-semibold text-white drop-shadow-lg">{{ $progressPercentage }}%</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <div class="text-center">
                            <div class="font-semibold text-green-600">{{ $completedContentsCount }}</div>
                            <div class="text-gray-500">Selesai</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-indigo-600">{{ $totalContentsInCourse - $completedContentsCount }}</div>
                            <div class="text-gray-500">Tersisa</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-gray-700">{{ $totalContentsInCourse }}</div>
                            <div class="text-gray-500">Total</div>
                        </div>
                    </div>

                    <!-- Completion Action -->
                    <div class="pt-4 border-t border-gray-100">
                        <div x-show="!contentCompleted" class="space-y-3">
                            <p class="text-sm text-gray-600 text-center">Tandai konten ini sebagai selesai?</p>
                            <button @click="markAsCompleted(); showProgress = false"
                                    class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 hover:scale-105">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tandai Selesai
                            </button>
                        </div>
                        <div x-show="contentCompleted" class="text-center">
                            <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 font-medium rounded-xl">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Konten Selesai ✨
                            </div>
                        </div>
                    </div>
                </div>

                <button @click="showProgress = false"
                        class="w-full mt-6 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    Tutup
                </button>
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
            margin-bottom: 1.25rem;
            line-height: 1.75;
        }

        .prose ul, .prose ol {
            margin: 1.25rem 0;
            padding-left: 1.625rem;
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

        .prose pre {
            background-color: #1f2937;
            color: #f9fafb;
            padding: 1.25rem;
            border-radius: 0.5rem;
            overflow-x: auto;
        }

        .prose a {
            color: #3b82f6;
            text-decoration: underline;
        }

        .prose a:hover {
            color: #1d4ed8;
        }

        .prose img {
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.25rem 0;
        }

        .prose th, .prose td {
            border: 1px solid #e5e7eb;
            padding: 0.75rem;
            text-align: left;
        }

        .prose th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        /* ✅ PERBAIKAN: Custom scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ✅ PERBAIKAN: Enhanced button animations */
        .group:hover {
            transform: translateY(-0.5px);
        }

        /* ✅ PERBAIKAN: Ensure bottom navigation never gets covered */
        .fixed {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* ✅ PERBAIKAN: Responsive Typography */
        @media (max-width: 640px) {
            .max-w-48 {
                max-width: 180px;
            }
        }

        /* ✅ PERBAIKAN: Enhanced backdrop blur support */
        .backdrop-blur-md {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        /* ✅ PERBAIKAN: Ensure no overlapping elements */
        .z-\[9999\] {
            z-index: 9999;
        }

        .z-\[10000\] {
            z-index: 10000;
        }
    </style>

    <script>
        // ✅ PERBAIKAN: Improved sidebar management for bottom bar
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebarWidth', 384); // 24rem = 384px
        });

        // ✅ PERBAIKAN: Auto-hide mobile sidebar when scrolling
        let lastScrollTop = 0;

        window.addEventListener('scroll', function() {
            if (window.innerWidth < 1024) {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    // Check if Alpine.js is available before using it
                    if (window.Alpine && window.Alpine.store) {
                        window.Alpine.store('sidebarOpen', false);
                    }
                }
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            }
        }, false);

        // ✅ PERBAIKAN: Enhanced keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Prevent conflicts with form inputs
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }

            // Arrow key navigation (with Ctrl)
            if (e.ctrlKey) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    const prevLink = document.querySelector('.fixed a[href*="contents.show"]');
                    if (prevLink) prevLink.click();
                }
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    const nextLink = document.querySelector('.fixed .bg-gradient-to-r a');
                    if (nextLink) nextLink.click();
                }
            }

            // Escape to toggle sidebar
            if (e.key === 'Escape') {
                e.preventDefault();
                // Use Alpine.js data if available
                const component = document.querySelector('[x-data]').__x_component;
                if (component) {
                    component.sidebarOpen = !component.sidebarOpen;
                }
            }
        });

        // ✅ PERBAIKAN: Prevent page scroll when modal is open
        document.addEventListener('alpine:init', () => {
            Alpine.data('contentData', () => ({
                showProgress: false,
                init() {
                    this.$watch('showProgress', value => {
                        if (value) {
                            document.body.style.overflow = 'hidden';
                        } else {
                            document.body.style.overflow = '';
                        }
                    });
                }
            }));
        });

        // ✅ PERBAIKAN: Handle window resize for bottom bar
        window.addEventListener('resize', function() {
            // Force re-calculation of bottom bar position
            if (window.innerWidth >= 1024) {
                // Desktop view - adjust bottom bar based on sidebar state
                const bottomBar = document.querySelector('.fixed.bottom-0');
                if (bottomBar) {
                    const sidebarOpen = document.querySelector('[x-data]').__x_component?.sidebarOpen;
                    bottomBar.style.left = sidebarOpen ? '384px' : '0px';
                }
            } else {
                // Mobile view - reset bottom bar
                const bottomBar = document.querySelector('.fixed.bottom-0');
                if (bottomBar) {
                    bottomBar.style.left = '0px';
                }
            }
        });
    </script>
</x-app-layout>
