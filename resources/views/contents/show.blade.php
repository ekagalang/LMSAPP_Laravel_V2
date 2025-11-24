<x-app-layout>
    @php
        // Siapkan variabel di luar atribut untuk menghindari konflik Blade di dalam x-data
        $user = Auth::user();
        $isTask = in_array($content->type, ['quiz', 'essay']);
        $isContentEffectivelyCompleted = false;

        if ($content->is_optional ?? false) {
            $isContentEffectivelyCompleted = $user->hasCompletedContent($content);
        } elseif ($content->type === 'quiz' && $content->quiz_id) {
            // Dianggap selesai jika ada percobaan yang lulus
            $isContentEffectivelyCompleted = $user->quizAttempts()->where('quiz_id', $content->quiz_id)->where('passed', true)->exists();
        } elseif ($content->type === 'essay') {
            // Essay dianggap selesai jika sudah ada submission (tidak perlu dinilai)
            $isContentEffectivelyCompleted = $user->essaySubmissions()->where('content_id', $content->id)->exists();
        } else {
            // Untuk konten biasa, gunakan helper yang konsisten dengan dashboard
            $isContentEffectivelyCompleted = $user->hasCompletedContent($content);
        }
    @endphp

    <div x-data="{
        sidebarOpen: false,
        showProgress: false,
        isTask: @json($isTask),
        contentCompleted: @json($isContentEffectivelyCompleted),

        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen },

        // Fungsi ini akan men-submit form untuk menandai selesai
        markAsCompleted() {
            if (!this.isTask) {
                document.getElementById('complete-form').submit();
            }
        }
    }"
    class="flex flex-col min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="fixed top-4 right-4 z-50 max-w-md" x-data="{ show: true }" x-show="show" x-transition>
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                        <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="fixed top-4 right-4 z-50 max-w-md" x-data="{ show: true }" x-show="show" x-transition>
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg shadow-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-amber-700">{{ session('warning') }}</p>
                        <button @click="show = false" class="ml-auto text-amber-400 hover:text-amber-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- [BARU] Form tersembunyi untuk menandai selesai (hanya untuk konten non-tugas) -->
        @if(!$isTask)
        <form id="complete-form" action="{{ route('contents.complete_and_continue', $content->id) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif

        <!-- Sidebar Backdrop Overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
             style="display: none;">
        </div>

        <!-- Mobile Header -->
        <div class="lg:hidden bg-white shadow-sm border-b p-4 flex items-center justify-between sticky top-0 z-30">
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
            class="fixed inset-y-0 top-0 left-0 w-full sm:w-96 h-screen bg-white flex-shrink-0 z-50 flex flex-col"
            style="box-shadow:
                0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 4px 6px -4px rgba(0, 0, 0, 0.1),
                8px 0 30px -5px rgba(99, 102, 241, 0.15),
                12px 0 40px -10px rgba(139, 92, 246, 0.1);">

            <!-- Sidebar Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold truncate">{{ $course->title }}</h3>
                        <p class="text-indigo-100 text-sm mt-1">Pembelajaran Interaktif</p>
                    </div>
                    <button @click="sidebarOpen = false" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                @php
                    $totalContentsInCourse = $course->lessons->flatMap->contents->count();
                    // Perbaikan: Use fresh query for completed count dengan logika essay yang baru
                    $completedContentsCount = 0;
                    foreach ($course->lessons as $lesson) {
                        foreach ($lesson->contents as $contentItem) {
                            if ($user->hasCompletedContent($contentItem)) {
                                $completedContentsCount++;
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

            <!-- Course Navigation with Custom Scroll -->
            <nav class="flex-1 overflow-y-auto p-6 pb-24 content-sidebar-scroll">
                @foreach ($course->lessons->sortBy('order') as $lesson)
                    <div class="mb-6 last:mb-2">
                        <!-- Lesson Header - Redesigned -->
                        <div class="flex items-center mb-3 pb-3 border-b-2 border-indigo-100">
                            <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl flex items-center justify-center text-sm font-bold mr-3 shadow-sm">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm leading-tight truncate">
                                    {{ $lesson->title }}
                                </h4>
                                @php
                                    $lessonContentsCount = $lesson->contents->count();
                                    // Perbaikan: Calculate lesson completed count dengan logika essay yang konsisten
                                    $lessonCompletedCount = 0;
                                    foreach ($lesson->contents as $contentItem) {
                                        if ($user->hasCompletedContent($contentItem)) {
                                            $lessonCompletedCount++;
                                        }
                                    }
                                    $lessonProgress = $lessonContentsCount > 0 ? round(($lessonCompletedCount / $lessonContentsCount) * 100) : 0;
                                @endphp
                                <div class="flex items-center mt-1 space-x-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $lessonProgress }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600 whitespace-nowrap">
                                        {{ $lessonCompletedCount }}/{{ $lessonContentsCount }}
                                    </span>
                                </div>
                            </div>
                            @if($lesson->is_optional ?? false)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-500 text-white uppercase tracking-wide shadow-sm">OPS</span>
                            @endif
                        </div>

                        <!-- Content List - Redesigned -->
                        <ul class="space-y-1.5">
                            @foreach ($lesson->contents->sortBy('order') as $c)
                                @php
                                    $cIsTask = in_array($c->type, ['quiz', 'essay']);

                                    // Perbaikan: Consistent completion check dengan logika essay baru
                                    if ($c->type === 'essay') {
                                        $submission = $user->essaySubmissions()->where('content_id', $c->id)->first();
                                        if ($submission) {
                                            // Untuk tampilan sidebar: show completed jika sudah submit (unlock criteria)
                                            $isCompleted = $submission->canUnlockNextContent();
                                        } else {
                                            $isCompleted = false;
                                        }
                                    } elseif ($c->type === 'quiz' && $c->quiz_id) {
                                        $isCompleted = $user->quizAttempts()->where('quiz_id', $c->quiz_id)->where('passed', true)->exists();
                                    } else {
                                        $isCompleted = $user->hasCompletedContent($c);
                                    }

                                    $isCurrent = $c->id === $content->id;
                                    $isUnlocked = $unlockedContents->contains('id', $c->id);
                                @endphp
                                <li>
                                    @if($isUnlocked)
                                        <a href="{{ route('contents.show', $c) }}"
                                           class="group block p-2.5 rounded-lg transition-all duration-200 border
                                                  {{ $isCurrent
                                                      ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-md border-indigo-400 scale-[1.02]'
                                                      : ($isCompleted
                                                          ? 'bg-green-50 hover:bg-green-100 text-green-900 border-green-200 hover:border-green-300'
                                                          : 'bg-white hover:bg-gray-50 text-gray-700 border-gray-200 hover:border-gray-300 hover:shadow-sm') }}">

                                            <div class="flex items-center space-x-2.5">
                                                <!-- Icon -->
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-base
                                                            {{ $isCurrent ? 'bg-white/20' : ($isCompleted ? 'bg-green-200/50' : 'bg-gray-100') }}">
                                                    @switch($c->type)
                                                        @case('video') 🎥 @break
                                                        @case('document') 📄 @break
                                                        @case('image') 🖼️ @break
                                                        @case('quiz') 🧠 @break
                                                        @case('essay') ✍️ @break
                                                        @default 📝
                                                    @endswitch
                                                </div>

                                                <!-- Content Info -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-sm truncate leading-tight">
                                                        {{ $c->title }}
                                                    </p>
                                                    <div class="flex items-center mt-0.5 space-x-1.5">
                                                        <span class="text-[10px] font-medium opacity-75 uppercase tracking-wide">
                                                            {{ ucfirst($c->type) }}
                                                        </span>
                                                        @if($c->is_optional ?? false)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-500 text-white uppercase">OPS</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Status Icon -->
                                                <div class="flex-shrink-0">
                                                    @if($isCurrent)
                                                        <div class="w-5 h-5 bg-white/30 rounded-full flex items-center justify-center">
                                                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                                        </div>
                                                    @elseif($isCompleted)
                                                        <div class="w-5 h-5 bg-green-500 text-white rounded-full flex items-center justify-center">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <div class="group block p-2.5 rounded-lg bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed opacity-60">
                                            <div class="flex items-center space-x-2.5">
                                                <!-- Lock Icon -->
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-gray-200">
                                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>

                                                <!-- Content Info -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-sm truncate leading-tight">
                                                        {{ $c->title }}
                                                    </p>
                                                    <div class="flex items-center mt-0.5 space-x-1.5">
                                                        <span class="text-[10px] font-medium opacity-75 uppercase tracking-wide">
                                                            Terkunci
                                                        </span>
                                                        @if($c->is_optional ?? false)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-400 text-white uppercase">OPS</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Lock Status -->
                                                <div class="flex-shrink-0">
                                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-full bg-gray-100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            <!-- Sticky Back Button at Bottom -->
            <div class="sticky bottom-0 p-4 border-t border-gray-200 bg-white shadow-2xl backdrop-blur-sm bg-white/95">
                <a href="javascript:void(0)" onclick="window.history.back()" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-medium rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
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
                        @if(!$isTask)
                            @if($canComplete)
                                <button @click="markAsCompleted()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-all duration-200 hover:scale-105 shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm">Tandai Selesai</span>
                                </button>
                            @else
                                <button disabled class="inline-flex items-center px-4 py-2 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed opacity-60">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span class="text-sm">Absensi Diperlukan</span>
                                </button>
                            @endif
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

            <!-- PERBAIKAN: Content Container dengan padding bottom yang cukup untuk bottom bar -->
            <div class="flex-1 overflow-y-auto pb-32">
                <div class="{{ $content->type === 'essay' ? 'max-w-6xl' : 'max-w-4xl' }} mx-auto p-6 lg:p-8">
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

                                @elseif($content->type == 'image' && ($content->images->count() || $content->file_path))
                                    @php $imageCount = $content->images->count(); @endphp
                                    @if($imageCount > 0)
                                        <div
                                            x-data='imageSlider(@json($content->images->map(fn($img) => Storage::url($img->file_path))->values()))'
                                            x-init="init()"
                                            class="select-none group">
                                            <!-- Viewport -->
                                            <div x-ref="viewport" class="relative overflow-hidden rounded-2xl shadow-2xl bg-white"
                                                 @mousemove="revealUI()" @mouseenter="showUI = true" @mouseleave="showUI = false"
                                                 @touchstart.passive="revealUI()">
                                                <!-- Edge fade for seamless look -->
                                                <div class="pointer-events-none absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-white to-transparent z-10"></div>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white to-transparent z-10"></div>

                                                <!-- Track -->
                                                <div
                                                    class="flex will-change-transform"
                                                    :class="transitioning && !dragging ? 'transition-transform duration-500 ease-in-out' : ''"
                                                    :style="trackStyle()"
                                                    @transitionend="onTransitionEnd"
                                                    @mouseenter="hover = true"
                                                    @mouseleave="hover = false"
                                                    @mousedown="onDown($event)"
                                                    @mousemove="onMove($event)"
                                                    @mouseup="onUp()"
                                                    @mouseleave="onUp()"
                                                    @touchstart.passive="onDown($event)"
                                                    @touchmove.passive="onMove($event)"
                                                    @touchend.passive="onUp()"
                                                >
                                                    <!-- Leading clone (last slide) -->
                                                    <div class="w-full flex-shrink-0">
                                                        <div class="aspect-[16/9] bg-white flex items-center justify-center">
                                                            <img loading="lazy" :src="slides[n-1]" alt="clone-last" class="w-full h-full object-contain" draggable="false">
                                                        </div>
                                                    </div>

                                                    <!-- Real slides -->
                                                    <template x-for="(src, i) in slides" :key="i">
                                                        <div class="w-full flex-shrink-0">
                                                            <div class="aspect-[16/9] bg-white flex items-center justify-center">
                                                                <img loading="lazy" :src="src" :alt="'Slide ' + (i+1)" class="w-full h-full object-contain" draggable="false">
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Trailing clone (first slide) -->
                                                    <div class="w-full flex-shrink-0">
                                                        <div class="aspect-[16/9] bg-white flex items-center justify-center">
                                                            <img loading="lazy" :src="slides[0]" alt="clone-first" class="w-full h-full object-contain" draggable="false">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Overlay controls (mobile always visible; desktop on hover) -->
                                                <div class="pointer-events-none absolute inset-0 flex items-center justify-between px-2 z-50 transition-opacity"
                                                     :class="showUI ? 'opacity-100' : 'opacity-100 md:opacity-0'">
                                                    <button @click.prevent="prev()" class="pointer-events-auto p-2 rounded-full bg-white/70 backdrop-blur-sm text-gray-800 shadow ring-1 ring-black/5 hover:bg-white">
                                                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                    </button>
                                                    <button @click.prevent="next()" class="pointer-events-auto p-2 rounded-full bg-white/70 backdrop-blur-sm text-gray-800 shadow ring-1 ring-black/5 hover:bg-white">
                                                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                    </button>
                                                </div>

                                                <!-- Overlay dots (mobile always visible; desktop on hover) -->
                                                <div class="absolute bottom-2 left-0 right-0 flex items-center justify-center z-50 transition-opacity"
                                                     :class="showUI ? 'opacity-100' : 'opacity-100 md:opacity-0'">
                                                    <div class="px-2 py-1 rounded-full bg-white/70 backdrop-blur-sm shadow ring-1 ring-black/5 flex items-center gap-1.5">
                                                        <template x-for="(src, i) in slides" :key="'dot-'+i">
                                                            <button @click="setSlide(i)" class="w-2.5 h-2.5 rounded-full"
                                                                :class="activeDot(i) ? 'bg-gray-900' : 'bg-gray-400/70 hover:bg-gray-500'"
                                                                :aria-label="'Slide '+(i+1)"></button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Thumbnails -->
                                            <template x-if="n > 1">
                                                <div class="mt-3 grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2">
                                                    <template x-for="(src, i) in slides" :key="'thumb-'+i">
                                                        <button @click="setSlide(i)" class="relative group rounded-lg overflow-hidden border"
                                                                :class="activeDot(i) ? 'border-indigo-500' : 'border-gray-200'">
                                                            <img loading="lazy" :src="src" :alt="'thumb '+(i+1)" class="w-full h-16 object-cover">
                                                            <span x-show="activeDot(i)" class="absolute inset-0 ring-2 ring-indigo-500" style="display:none"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>

                                            <p class="text-sm text-gray-500 mt-4 text-center">{{ $content->title }}</p>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <div class="inline-block rounded-2xl overflow-hidden shadow-2xl">
                                                <img loading="lazy" src="{{ Storage::url($content->file_path) }}"
                                                     alt="{{ $content->title }}"
                                                     class="max-w-full h-auto max-h-96 object-contain">
                                            </div>
                                            <p class="text-sm text-gray-500 mt-4">{{ $content->title }}</p>
                                        </div>
                                    @endif

                                @elseif($content->type == 'document' && $content->file_path)
                                    @php
                                        $accessType = $content->document_access_type ?? 'both';
                                        $fileUrl = Storage::url($content->file_path);
                                        $fullFileUrl = url($fileUrl);
                                        $fileName = basename($content->file_path);
                                        $fileExtension = strtolower(pathinfo($content->file_path, PATHINFO_EXTENSION));

                                        // Cek apakah file bisa di-preview (PDF, Office docs, images)
                                        $previewableExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
                                        $isPreviewable = in_array($fileExtension, $previewableExtensions);
                                    @endphp

                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-100">
                                        <div class="flex items-center justify-center mb-6">
                                            <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center">
                                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="text-center mb-6">
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Dokumen Pembelajaran</h3>
                                            <p class="text-gray-600 mb-2">{{ $fileName }}</p>
                                            @php
                                                $badgeClass = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ';
                                                $badgeText = '';
                                                if ($accessType === 'both') {
                                                    $badgeClass .= 'bg-indigo-100 text-indigo-700';
                                                    $badgeText = '👁️💾 Preview & Download';
                                                } elseif ($accessType === 'download_only') {
                                                    $badgeClass .= 'bg-green-100 text-green-700';
                                                    $badgeText = '💾 Download Saja';
                                                } else {
                                                    $badgeClass .= 'bg-purple-100 text-purple-700';
                                                    $badgeText = '👁️ Preview Saja';
                                                }
                                            @endphp
                                            <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                                        </div>

                                        @if($content->documents && $content->documents->count())
                                            <div class="mb-6">
                                                <div class="bg-white rounded-xl border p-4">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-sm font-semibold text-gray-700">Lampiran Dokumen</p>
                                                        <a href="#" class="text-xs text-indigo-600 hover:underline js-doc-preview" data-url="{{ $fileUrl }}" data-full-url="{{ $fullFileUrl }}" data-ext="{{ $fileExtension }}">Tampilkan file utama</a>
                                                    </div>
                                                    <ul class="space-y-2 text-sm">
                                                        @foreach($content->documents as $doc)
                                                            @continue($doc->file_path === $content->file_path)
                                                            @php
                                                                $docName = $doc->original_name ?? basename($doc->file_path);
                                                                $docUrl = Storage::url($doc->file_path);
                                                                $docFullUrl = url($docUrl);
                                                                $docExt = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                                            @endphp
                                                            <li class="flex items-center justify-between">
                                                                <div class="flex items-center gap-3 min-w-0">
                                                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8"/></svg>
                                                                    <span class="truncate" title="{{ $docName }}">{{ $docName }}</span>
                                                                </div>
                                                                <div class="flex items-center gap-3 flex-shrink-0">
                                                                    @if($accessType === 'download_only')
                                                                        <a href="{{ $docUrl }}" download class="text-green-600 hover:underline">Unduh</a>
                                                                    @elseif($accessType === 'preview_only')
                                                                        @if($docExt === 'pdf')
                                                                            @if($fileExtension === 'pdf')
                                                                                <a href="#" class="text-indigo-600 hover:underline js-doc-preview" data-url="{{ $docUrl }}" data-full-url="{{ $docFullUrl }}" data-ext="{{ $docExt }}">Preview</a>
                                                                            @else
                                                                                <a href="#" class="text-indigo-600 hover:underline js-doc-preview" data-url="{{ $docUrl }}" data-full-url="{{ $docFullUrl }}" data-ext="{{ $docExt }}">Preview</a>
                                                                            @endif
                                                                        @else
                                                                             <a href="#" class="text-indigo-600 hover:underline js-doc-preview" data-url="{{ $docUrl }}" data-full-url="{{ $docFullUrl }}" data-ext="{{ $docExt }}">Preview</a>
                                                                        @endif
                                                                    @else
                                                                        @if($docExt === 'pdf')
                                                                            @if($fileExtension === 'pdf')
                                                                                <a href="#" class="text-indigo-600 hover:underline js-doc-preview" data-url="{{ $docUrl }}" data-full-url="{{ $docFullUrl }}" data-ext="{{ $docExt }}">Preview</a>
                                                                            @else
                                                                                <a href="#" class="text-indigo-600 hover:underline js-doc-preview" data-url="{{ $docUrl }}" data-full-url="{{ $docFullUrl }}" data-ext="{{ $docExt }}">Preview</a>
                                                                            @endif
                                                                        @else
                                                                             <a href="#" class="text-indigo-600 hover:underline js-doc-preview" data-url="{{ $docUrl }}" data-full-url="{{ $docFullUrl }}" data-ext="{{ $docExt }}">Preview</a>
                                                                        @endif
                                                                        <a href="{{ $docUrl }}" download class="text-green-600 hover:underline">Unduh</a>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Preview Section (jika bisa di-preview dan akses type bukan download_only) --}}
                                        @if($isPreviewable && $accessType !== 'download_only')
                                            <div class="mb-6 bg-white rounded-xl overflow-hidden shadow-lg" x-data="{ loading: true, error: false }">
                                                <div class="aspect-[4/3] md:aspect-video relative">
                                                    {{-- Loading State --}}
                                                    <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-gray-100">
                                                        <div class="text-center">
                                                            <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                            <p class="text-sm text-gray-600">Memuat preview dokumen...</p>
                                                        </div>
                                                    </div>

                                                    {{-- Error State --}}
                                                    <div x-show="error" class="absolute inset-0 flex items-center justify-center bg-yellow-50" style="display: none;">
                                                        <div class="text-center p-4">
                                                            <svg class="w-12 h-12 text-yellow-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                            </svg>
                                                            <p class="text-sm text-yellow-800 mb-2">Preview tidak dapat dimuat</p>
                                                            <p class="text-xs text-yellow-600">Silakan gunakan tombol download untuk melihat dokumen</p>
                                                        </div>
                                                    </div>

                                                    {{-- PDF Preview using PDF.js to avoid iframe blocking in production --}}
                                                    @if($fileExtension === 'pdf')
                                                        <div id="doc-pdf-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100">
                                                            <div class="w-12 h-12 border-4 border-gray-300 border-t-indigo-600 rounded-full animate-spin mb-4"></div>
                                                            <p class="text-sm text-gray-600">Memuat preview dokumen...</p>
                                                        </div>
                                                        <div id="doc-pdf-viewer" class="hidden absolute inset-0 overflow-auto">
                                                            <div class="min-h-full p-4 md:p-6">
                                                                <div id="doc-pdf-pages" class="mx-auto space-y-6" style="max-width: 1000px;"></div>
                                                            </div>
                                                        </div>
                                                        <div id="doc-embed-viewer" class="hidden absolute inset-0">
                                                            <iframe id="doc-embed-iframe" class="w-full h-full border-0"></iframe>
                                                        </div>
                                                        <div id="doc-pdf-fallback" class="hidden absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-yellow-50">
                                                            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                                                                <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                            </div>
                                                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Preview tidak tersedia</h3>
                                                            <p class="text-sm text-gray-600 mb-4">Browser memblokir tampilan tersemat. Anda masih dapat membuka di tab baru atau mengunduhnya.</p>
                                                            <div class="flex flex-col sm:flex-row gap-3">
                                                                <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                                    </svg>
                                                                    Buka di Tab Baru
                                                                </a>
                                                                <a href="{{ $fileUrl }}" download class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                    </svg>
                                                                    Unduh PDF
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
                                                        <script>
                                                            (function() {
                                                                try {
                                                                    if (window.pdfjsLib) {
                                                                        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                                                                    }
                                                                    const pagesEl = document.getElementById('doc-pdf-pages');
                                                                    const loadingEl = document.getElementById('doc-pdf-loading');
                                                                    const pdfViewerEl = document.getElementById('doc-pdf-viewer');
                                                                    const embedViewerEl = document.getElementById('doc-embed-viewer');
                                                                    const embedIframe = document.getElementById('doc-embed-iframe');
                                                                    const fallbackEl = document.getElementById('doc-pdf-fallback');

                                                                    const clearPages = () => { if (!pagesEl) return; while (pagesEl.firstChild) pagesEl.removeChild(pagesEl.firstChild); };
                                                                    const showLoading = () => {
                                                                        if (loadingEl) loadingEl.classList.remove('hidden');
                                                                        if (pdfViewerEl) pdfViewerEl.classList.add('hidden');
                                                                        if (embedViewerEl) embedViewerEl.classList.add('hidden');
                                                                        if (fallbackEl) fallbackEl.classList.add('hidden');
                                                                    };
                                                                    const showPdfViewer = () => {
                                                                        if (loadingEl) loadingEl.classList.add('hidden');
                                                                        if (embedViewerEl) embedViewerEl.classList.add('hidden');
                                                                        if (fallbackEl) fallbackEl.classList.add('hidden');
                                                                        if (pdfViewerEl) pdfViewerEl.classList.remove('hidden');
                                                                    };
                                                                    const showEmbedViewer = () => {
                                                                        if (loadingEl) loadingEl.classList.add('hidden');
                                                                        if (pdfViewerEl) pdfViewerEl.classList.add('hidden');
                                                                        if (fallbackEl) fallbackEl.classList.add('hidden');
                                                                        if (embedViewerEl) embedViewerEl.classList.remove('hidden');
                                                                    };
                                                                    const showFallback = () => {
                                                                        if (loadingEl) loadingEl.classList.add('hidden');
                                                                        if (pdfViewerEl) pdfViewerEl.classList.add('hidden');
                                                                        if (embedViewerEl) embedViewerEl.classList.add('hidden');
                                                                        if (fallbackEl) fallbackEl.classList.remove('hidden');
                                                                    };

                                                                    if (!window.pdfjsLib) { showFallback(); return; }

                                                                    window.loadDocumentPdf = function(pdfUrl) {
                                                                        try {
                                                                            showLoading();
                                                                            clearPages();
                                                                            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                                                                                if (!pagesEl) { throw new Error('No pages container'); }
                                                                                const total = pdf.numPages;
                                                                                let firstRendered = false;
                                                                                const renderPage = function(num) {
                                                                                    return pdf.getPage(num).then(function(page) {
                                                                                        const containerWidth = pagesEl.clientWidth || 800;
                                                                                        const initialViewport = page.getViewport({ scale: 1.0 });
                                                                                        const scale = Math.min(2.0, containerWidth / initialViewport.width);
                                                                                        const viewport = page.getViewport({ scale: scale });
                                                                                        const wrapper = document.createElement('div');
                                                                                        wrapper.className = 'bg-white rounded-xl shadow-xl overflow-hidden flex justify-center';
                                                                                        const canvas = document.createElement('canvas');
                                                                                        canvas.className = 'max-w-full h-auto';
                                                                                        const ctx = canvas.getContext('2d');
                                                                                        canvas.width = Math.floor(viewport.width);
                                                                                        canvas.height = Math.floor(viewport.height);
                                                                                        wrapper.appendChild(canvas);
                                                                                        pagesEl.appendChild(wrapper);
                                                                                        const renderContext = { canvasContext: ctx, viewport: viewport };
                                                                                        return page.render(renderContext).promise.then(function() {
                                                                                            if (!firstRendered) { firstRendered = true; showPdfViewer(); }
                                                                                        });
                                                                                    });
                                                                                };
                                                                                let chain = Promise.resolve();
                                                                                for (let i = 1; i <= total; i++) {
                                                                                    chain = chain.then(() => renderPage(i));
                                                                                }
                                                                                return chain;
                                                                            }).catch(function() { showFallback(); });
                                                                        } catch (e) { showFallback(); }
                                                                    };

                                                                    window.loadDocumentEmbed = function(embedUrl) {
                                                                        try {
                    if (!embedIframe) { showFallback(); return; }
                    showLoading();
                    embedIframe.onload = function() { showEmbedViewer(); };
                    embedIframe.src = embedUrl;
                } catch (e) { showFallback(); }
            };

            // Initial load with main file
            @if($fileExtension === 'pdf')
                window.loadDocumentPdf("{{ $fileUrl }}");
            @else
                window.loadDocumentEmbed("https://docs.google.com/viewer?url={{ urlencode($fullFileUrl) }}&embedded=true");
            @endif

            // Hook preview links (any ext)
            document.addEventListener('click', function(e){
                const t = e.target.closest('.js-doc-preview');
                if (t) {
                    e.preventDefault();
                    const ext = (t.getAttribute('data-ext') || '').toLowerCase();
                    const url = t.getAttribute('data-url');
                    const full = t.getAttribute('data-full-url') || url;
                    if (ext === 'pdf') {
                        window.loadDocumentPdf(url);
                        (pdfViewerEl || embedViewerEl)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        const embed = 'https://docs.google.com/viewer?url=' + encodeURIComponent(full) + '&embedded=true';
                        window.loadDocumentEmbed(embed);
                        (embedViewerEl || pdfViewerEl)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
                                                                } catch (e) {
                                                                    const fb = document.getElementById('doc-pdf-fallback');
                                                                    if (fb) { fb.classList.remove('hidden'); }
                                                                    const ld = document.getElementById('doc-pdf-loading');
                                                                    if (ld) { ld.classList.add('hidden'); }
                                                                }
                                                            })();
                                                        </script>
                                                    @else
                                                        {{-- Google Docs Viewer untuk Office files (dynamic) --}}
                                                        <div id="doc-embed-viewer" class="absolute inset-0">
                                                            <iframe id="doc-embed-iframe"
                                                                    src="https://docs.google.com/viewer?url={{ urlencode($fullFileUrl) }}&embedded=true"
                                                                    class="w-full h-full border-0"
                                                                    x-on:load="loading = false"
                                                                    x-on:error="error = true; loading = false"></iframe>
                                                        </div>
                                                        <script>
                                                            (function(){
                                                                document.addEventListener('click', function(e){
                                                                    const t = e.target.closest('.js-doc-preview');
                                                                    if (!t) return;
                                                                    e.preventDefault();
                                                                    const full = t.getAttribute('data-full-url') || t.getAttribute('data-url');
                                                                    const embed = 'https://docs.google.com/viewer?url=' + encodeURIComponent(full) + '&embedded=true';
                                                                    const f = document.getElementById('doc-embed-iframe');
                                                                    if (f) { f.src = embed; }
                                                                });
                                                            })();
                                                        </script>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Download Button (jika akses type bukan preview_only) --}}
                                        @if($accessType !== 'preview_only')
                                            <div class="text-center">
                                                <a href="{{ $fileUrl }}" download
                                                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Download Dokumen
                                                </a>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <div class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-500 rounded-xl">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                    </svg>
                                                    Download Tidak Tersedia
                                                </div>
                                                <p class="text-xs text-gray-500 mt-2">Dokumen ini hanya bisa dilihat preview</p>
                                            </div>
                                        @endif

                                        {{-- Info message jika file tidak bisa di-preview --}}
                                        @if(!$isPreviewable && $accessType !== 'download_only')
                                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                <p class="text-sm text-yellow-800 text-center">
                                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Preview tidak tersedia untuk tipe file .{{ $fileExtension }}
                                                </p>
                                            </div>
                                        @endif
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

                                    {{-- Essay Questions Section with Autosave --}}
                                    @include('contents.partials.essay-section-improved')

                                @elseif($content->type == 'quiz' && $content->quiz)
                                    <!-- PERBAIKAN: Tampilkan quiz content dengan benar -->
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

                                        {{-- Scheduling Status Display --}}
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
                </div>

                <!-- Discussion Section - Always max-w-4xl -->
                <div class="max-w-4xl mx-auto px-6 lg:px-8 pb-6">
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

        <!-- PERBAIKAN UTAMA: Bottom Navigation dengan positioning yang lebih robust -->
        <div x-show="!sidebarOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-full"
             class="fixed bottom-0 left-0 right-0 bg-white/98 backdrop-blur-md border-t border-gray-200 shadow-2xl z-[9999] transition-all duration-300 ease-in-out">
            @php
                // Perbaikan: Mendapatkan konten dalam urutan yang benar
                $allContents = $orderedContents; // Gunakan data yang sudah diurutkan dari controller
                $currentIndex = $allContents->search(function($item) use ($content) {
                    return $item->id === $content->id;
                });

                $previousContent = $currentIndex > 0 ? $allContents->get($currentIndex - 1) : null;
                $nextContent = ($currentIndex !== false && $currentIndex < $allContents->count() - 1) ? $allContents->get($currentIndex + 1) : null;

                // Perbaikan LOGIC: Untuk quiz, cek apakah sudah lulus. Untuk essay, cek apakah sudah submit.
                if ($content->type === 'quiz' && $content->quiz_id) {
                    $canGoNext = $user->quizAttempts()
                        ->where('quiz_id', $content->quiz_id)
                        ->where('passed', true)
                        ->exists() && $nextContent;
                } elseif ($content->type === 'essay') {
                    // PERUBAHAN: Essay bisa lanjut setelah submit
                    $canGoNext = $user->essaySubmissions()
                        ->where('content_id', $content->id)
                        ->exists() && $nextContent;
                } else {
                    $canGoNext = $isContentEffectivelyCompleted && $nextContent;
                }

                // FITUR BARU: Cek apakah ini konten terakhir dan semua sudah selesai
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
                            $isContentDone = $user->hasCompletedContent($courseContent);
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
                            @if ($canGoNext || ((($content->is_optional ?? false)) && $nextContent))
                                <form action="{{ route('contents.complete_and_continue', $content->id) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg group">
                                    <span class="text-sm mr-2">Selanjutnya</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    </button>
                                </form>
                            @elseif((!$nextContent) && ($isContentEffectivelyCompleted || (($content->is_optional ?? false))))
                                <!-- FITUR BARU: Cek apakah semua kursus sudah selesai -->
                                @if($isAllCourseCompleted)
                                    <form action="{{ route('contents.complete_and_continue', $content->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                               class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                                Selesaikan Kursus
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="javascript:void(0)" onclick="window.history.back()"
                                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                        <span class="text-sm mr-2">Kembali ke Dashboard</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </a>
                                @endif
                            @elseif(!$isContentEffectivelyCompleted && !$isTask)
                                @if($canComplete)
                                    <button @click="markAsCompleted()"
                                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md transition-all duration-200 hover:scale-105">
                                        Tandai Selesai
                                    </button>
                                @else
                                    <div class="w-full p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-amber-800">Absensi Diperlukan</p>
                                                <p class="text-xs text-amber-700 mt-1">Anda perlu melakukan absensi terlebih dahulu untuk dapat melanjutkan ke konten berikutnya.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- TAMBAHAN: Pesan untuk quiz yang belum diselesaikan -->
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
                            @if ($canGoNext || ((($content->is_optional ?? false)) && $nextContent))
                                <form action="{{ route('contents.complete_and_continue', $content->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                    <span class="mr-2">Selanjutnya</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    </button>
                                </form>
                            @elseif ((!$nextContent) && ($isContentEffectivelyCompleted || (($content->is_optional ?? false))))
                                {{-- FIX: Simple completion button - let controller handle the logic --}}
                                <form action="{{ route('contents.complete_and_continue', $content->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 hover:scale-105">
                                            Selesai & Lanjutkan
                                    </button>
                                </form>
                            @elseif (!$isContentEffectivelyCompleted && !$isTask)
                                @if($canComplete)
                                    <button @click="markAsCompleted()"
                                            class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 hover:scale-105">
                                        Tandai Selesai untuk Lanjut
                                    </button>
                                @else
                                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-amber-800">Absensi Diperlukan</p>
                                                <p class="text-xs text-amber-700 mt-1">Anda perlu melakukan absensi terlebih dahulu sebelum dapat melanjutkan.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{-- TAMBAHAN: Pesan untuk desktop --}}
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
                            @if($canComplete)
                                <p class="text-sm text-gray-600 text-center">Tandai konten ini sebagai selesai?</p>
                                <button @click="markAsCompleted(); showProgress = false"
                                        class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 hover:scale-105">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Tandai Selesai
                                </button>
                            @else
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                    <p class="text-xs text-amber-800 text-center">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Absensi diperlukan
                                    </p>
                                </div>
                            @endif
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

        /* Custom Scrollbar for Content Sidebar */
        .content-sidebar-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .content-sidebar-scroll::-webkit-scrollbar-track {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 10px;
            margin: 4px 0;
        }

        .content-sidebar-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            border: 2px solid #f1f5f9;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
        }

        .content-sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #4f46e5, #7c3aed);
            box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
        }

        /* For Firefox */
        .content-sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #6366f1 #f1f5f9;
        }

        /* Smooth scroll behavior */
        .content-sidebar-scroll {
            scroll-behavior: smooth;
        }

        /* General scrollbar for other elements */
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

        /* Perbaikan: Enhanced button animations */
        .group:hover {
            transform: translateY(-0.5px);
        }

        /* Perbaikan: Ensure bottom navigation never gets covered */
        .fixed {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Perbaikan: Responsive Typography */
        @media (max-width: 640px) {
            .max-w-48 {
                max-width: 180px;
            }
        }

        /* Perbaikan: Enhanced backdrop blur support */
        .backdrop-blur-md {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        /* Perbaikan: Ensure no overlapping elements */
        .z-\[9999\] {
            z-index: 9999;
        }

        .z-\[10000\] {
            z-index: 10000;
        }
    </style>

    <script>
        // Image slider factory to avoid JSON quoting issues in x-data
        function imageSlider(slides) {
            return {
                slides: Array.isArray(slides) ? slides : [],
                index: 1, // starts after the leading clone
                transitioning: true,
                dragging: false,
                startX: 0,
                deltaX: 0,
                hover: false,
                autoplay: null,
                init() { /* no autoplay as requested */ },
                showUI: false,
                uiTimer: null,
                revealUI() { this.showUI = true; if (this.uiTimer) clearTimeout(this.uiTimer); this.uiTimer = setTimeout(() => { this.showUI = false; }, 1800); },
                get n() { return this.slides.length; },
                trackStyle() {
                    const vw = this.$refs.viewport ? this.$refs.viewport.clientWidth : 1;
                    const offsetPct = this.dragging && vw ? (this.deltaX / vw * 100) : 0;
                    const x = -(this.index * 100) + offsetPct;
                    return `transform: translate3d(${x}%,0,0);`;
                },
                onTransitionEnd() {
                    if (this.index === 0) { this.transitioning = false; this.index = this.n; this.$nextTick(() => this.transitioning = true); }
                    if (this.index === this.n + 1) { this.transitioning = false; this.index = 1; this.$nextTick(() => this.transitioning = true); }
                },
                next() { this.index++; },
                prev() { this.index--; },
                setSlide(i) { this.index = i + 1; },
                onDown(e) { this.dragging = true; this.startX = (e.touches ? e.touches[0].clientX : e.clientX); this.deltaX = 0; },
                onMove(e) { if (!this.dragging) return; const x = (e.touches ? e.touches[0].clientX : e.clientX); this.deltaX = x - this.startX; },
                onUp() {
                    if (!this.dragging) return; const w = this.$refs.viewport?.clientWidth || 1; const t = w * 0.15;
                    if (this.deltaX < -t) this.next(); else if (this.deltaX > t) this.prev();
                    this.dragging = false; this.deltaX = 0;
                },
                activeDot(i) {
                    let cur = this.index - 1; if (cur < 0) cur = this.n - 1; if (cur >= this.n) cur = 0; return cur === i;
                }
            };
        }
        // Perbaikan: Improved sidebar management for bottom bar
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebarWidth', 384); // 24rem = 384px
        });

        // Sidebar is now a floating overlay, no need for auto-hide on scroll

        // Perbaikan: Enhanced keyboard shortcuts
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

        // Perbaikan: Prevent page scroll when modal or sidebar is open
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

        // Watch for sidebar state changes to prevent body scroll
        document.addEventListener('alpine:initialized', () => {
            const alpineComponent = document.querySelector('[x-data]').__x;
            if (alpineComponent) {
                alpineComponent.$watch('sidebarOpen', (value) => {
                    if (value) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                });
            }
        });

        // Perbaikan: Handle window resize - sidebar is now always floating, no need to adjust bottom bar
        window.addEventListener('resize', function() {
            // Sidebar is now floating overlay, no layout adjustments needed
        });
    </script>
</x-app-layout>
