<x-app-layout>
    <div x-data="essayAttempt()" class="flex flex-col lg:flex-row min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">

        <!-- Sidebar Navigation -->
        <aside class="fixed lg:static inset-y-0 left-0 w-full sm:w-96 bg-white shadow-2xl lg:shadow-xl border-r border-gray-200 flex-shrink-0 z-50 lg:z-20 flex flex-col"
               x-show="sidebarOpen"
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-300 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full">

            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold truncate">{{ $content->title }}</h3>
                        <p class="text-purple-100 text-sm mt-1">Essay Questions</p>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span>Progress</span>
                        <span class="font-semibold" x-text="progressPercentage + '%'"></span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-gradient-to-r from-yellow-400 to-green-400 h-2 rounded-full transition-all duration-500"
                             :style="'width: ' + progressPercentage + '%'"></div>
                    </div>
                    <p class="text-xs text-purple-100 mt-1">
                        <span x-text="answeredCount"></span>/<span x-text="totalQuestions"></span> pertanyaan terjawab
                    </p>
                </div>
            </div>

            <!-- Questions List -->
            <nav class="flex-1 overflow-y-auto p-6 essay-sidebar-scroll">
                <div class="space-y-2">
                    @foreach($questions as $index => $item)
                        <button @click="goToQuestion({{ $index }})"
                                class="w-full text-left p-3 rounded-lg transition-all duration-200 border"
                                :class="{
                                    'bg-gradient-to-r from-purple-500 to-indigo-600 text-white shadow-md border-purple-400 scale-[1.02]': currentQuestion === {{ $index }},
                                    'bg-green-50 border-green-200 text-green-900': currentQuestion !== {{ $index }} && questionStatus[{{ $index }}].answered,
                                    'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300': currentQuestion !== {{ $index }} && !questionStatus[{{ $index }}].answered
                                }">
                            <div class="flex items-center space-x-3">
                                <!-- Question Number -->
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 font-bold"
                                     :class="{
                                         'bg-white/20 text-white': currentQuestion === {{ $index }},
                                         'bg-green-200 text-green-700': currentQuestion !== {{ $index }} && questionStatus[{{ $index }}].answered,
                                         'bg-gray-100 text-gray-600': currentQuestion !== {{ $index }} && !questionStatus[{{ $index }}].answered
                                     }">
                                    {{ $index + 1 }}
                                </div>

                                <!-- Question Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm truncate">Soal {{ $index + 1 }}</p>
                                    <p class="text-xs opacity-75">
                                        <span x-show="questionStatus[{{ $index }}].answered">✓ Terjawab</span>
                                        <span x-show="!questionStatus[{{ $index }}].answered">Belum dijawab</span>
                                    </p>
                                </div>

                                <!-- Status Indicator -->
                                <div class="flex-shrink-0">
                                    <template x-if="currentQuestion === {{ $index }}">
                                        <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                    </template>
                                    <template x-if="currentQuestion !== {{ $index }} && questionStatus[{{ $index }}].answered">
                                        <div class="w-5 h-5 bg-green-500 text-white rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="currentQuestion !== {{ $index }} && !questionStatus[{{ $index }}].answered">
                                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full"></div>
                                    </template>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </nav>

            <!-- Sticky Back Button -->
            <div class="sticky bottom-0 p-4 border-t border-gray-200 bg-white shadow-2xl backdrop-blur-sm bg-white/95">
                <a href="{{ route('contents.show', $content) }}"
                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-medium rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-h-screen lg:min-h-0">
            <!-- Header (Desktop) -->
            <header class="hidden lg:flex items-center justify-between p-6 bg-white border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl bg-purple-600 text-white hover:bg-purple-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $content->title }}</h1>
                        <p class="text-gray-600 text-sm">Essay Assignment</p>
                    </div>
                </div>

                <!-- Auto-save Indicator -->
                <div class="flex items-center space-x-3">
                    <div x-show="saving" class="flex items-center text-blue-600 text-sm">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </div>
                    <div x-show="!saving && lastSaved" class="text-green-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="'Tersimpan ' + lastSaved"></span>
                    </div>
                </div>
            </header>

            <!-- Mobile Header -->
            <div class="lg:hidden bg-white shadow-sm border-b p-4 flex items-center justify-between sticky top-0 z-40">
                <button @click="sidebarOpen = true" class="p-2 rounded-xl bg-purple-600 text-white hover:bg-purple-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex-1 mx-4">
                    <h1 class="text-lg font-bold text-gray-900 truncate">Soal <span x-text="currentQuestion + 1"></span></h1>
                </div>
                <div x-show="saving" class="text-blue-600">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Question Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto">
                    @foreach($questions as $index => $item)
                        <div x-show="currentQuestion === {{ $index }}"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform translate-x-4"
                             x-transition:enter-end="opacity-100 transform translate-x-0"
                             class="space-y-6">

                            <!-- Question Card -->
                            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6 text-white">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-2xl font-bold">Soal {{ $index + 1 }}</h2>
                                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                                            {{ $item['question']->max_score }} poin
                                        </span>
                                    </div>
                                </div>

                                <div class="p-8">
                                    <div class="prose prose-lg max-w-none mb-8">
                                        {!! $item['question']->question !!}
                                    </div>

                                    <!-- Answer Textarea -->
                                    <div class="mt-6">
                                        <label class="block text-sm font-bold text-gray-700 mb-3">
                                            Jawaban Anda:
                                        </label>
                                        <textarea x-model="answers[{{ $index }}]"
                                                  @input="handleInput({{ $index }})"
                                                  @blur="autoSaveAnswer({{ $index }})"
                                                  rows="12"
                                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all resize-none"
                                                  placeholder="Tuliskan jawaban Anda di sini..."
                                                  data-question-id="{{ $item['question']->id }}"></textarea>

                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-sm text-gray-500">
                                                <span x-text="answers[{{ $index }}] ? answers[{{ $index }}].length : 0"></span> karakter
                                            </p>
                                            <p x-show="questionStatus[{{ $index }}].saving" class="text-sm text-blue-600">
                                                Menyimpan...
                                            </p>
                                            <p x-show="!questionStatus[{{ $index }}].saving && questionStatus[{{ $index }}].lastSaved"
                                               class="text-sm text-green-600">
                                                ✓ Tersimpan
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="flex items-center justify-between gap-4 bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                                <button @click="previousQuestion()"
                                        x-show="currentQuestion > 0"
                                        class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Sebelumnya
                                </button>

                                <div class="flex-1 text-center">
                                    <p class="text-sm text-gray-600 mb-1">Soal</p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        <span x-text="currentQuestion + 1"></span>/<span x-text="totalQuestions"></span>
                                    </p>
                                </div>

                                <button @click="nextQuestion()"
                                        x-show="currentQuestion < totalQuestions - 1"
                                        class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                    Selanjutnya
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <button @click="confirmSubmit()"
                                        x-show="currentQuestion === totalQuestions - 1"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Kumpulkan Essay
                                </button>
                            </div>
                        </div>
                    @endforeach

                    @if($questions->isEmpty())
                        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-yellow-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Soal</h3>
                            <p class="text-gray-600">Instruktur belum menambahkan soal untuk essay ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>

        <!-- Form untuk submit -->
        <form id="complete-form" action="{{ route('essays.complete', $content) }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <style>
        /* Custom Scrollbar untuk Sidebar */
        .essay-sidebar-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .essay-sidebar-scroll::-webkit-scrollbar-track {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 10px;
            margin: 4px 0;
        }

        .essay-sidebar-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #8b5cf6, #6366f1);
            border-radius: 10px;
            border: 2px solid #f1f5f9;
        }

        .essay-sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #7c3aed, #4f46e5);
        }

        .essay-sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #8b5cf6 #f1f5f9;
            scroll-behavior: smooth;
        }

        /* Prose styling */
        .prose { color: #374151; }
        .prose h1, .prose h2, .prose h3 { color: #111827; font-weight: 600; }
        .prose p { margin-bottom: 1rem; line-height: 1.75; }
        .prose ul, .prose ol { margin: 1rem 0; padding-left: 1.5rem; }
        .prose li { margin: 0.5rem 0; }
    </style>

    <script>
        function essayAttempt() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                currentQuestion: 0,
                totalQuestions: {{ $questions->count() }},
                answers: @json($questions->map(fn($item) => $item['answer']->answer ?? '')->toArray()),
                questionStatus: @json($questions->map(function($item) {
                    return [
                        'answered' => (bool) ($item['is_answered'] ?? false),
                        'saving' => false,
                        'lastSaved' => null,
                        'questionId' => (int) ($item['question']->id ?? 0)
                    ];
                })->values()->toArray()),
                saving: false,
                lastSaved: null,
                autoSaveTimer: null,

                init() {
                    // Load from localStorage
                    this.loadFromLocalStorage();

                    // Auto-save every 30 seconds
                    setInterval(() => {
                        this.autoSaveAll();
                    }, 30000);

                    // Save to localStorage on answers change
                    this.$watch('answers', () => {
                        this.saveToLocalStorage();
                        this.updateProgress();
                    });

                    // Keyboard shortcuts
                    document.addEventListener('keydown', (e) => {
                        if (e.ctrlKey || e.metaKey) {
                            if (e.key === 'ArrowLeft') {
                                e.preventDefault();
                                this.previousQuestion();
                            } else if (e.key === 'ArrowRight') {
                                e.preventDefault();
                                this.nextQuestion();
                            } else if (e.key === 's') {
                                e.preventDefault();
                                this.autoSaveAnswer(this.currentQuestion);
                            }
                        }
                    });

                    this.updateProgress();
                },

                goToQuestion(index) {
                    this.currentQuestion = index;
                    this.sidebarOpen = window.innerWidth >= 1024;
                },

                nextQuestion() {
                    if (this.currentQuestion < this.totalQuestions - 1) {
                        this.currentQuestion++;
                    }
                },

                previousQuestion() {
                    if (this.currentQuestion > 0) {
                        this.currentQuestion--;
                    }
                },

                handleInput(index) {
                    // Debounce auto-save
                    clearTimeout(this.autoSaveTimer);
                    this.autoSaveTimer = setTimeout(() => {
                        this.autoSaveAnswer(index);
                    }, 2000);
                },

                async autoSaveAnswer(index) {
                    const answer = this.answers[index];
                    const questionId = this.questionStatus[index].questionId;

                    if (!answer || answer.trim() === '') return;

                    this.questionStatus[index].saving = true;
                    this.saving = true;

                    try {
                        const response = await fetch('{{ route("essays.auto-save", $content) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                question_id: questionId,
                                answer: answer
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.questionStatus[index].lastSaved = 'baru saja';
                            this.questionStatus[index].answered = true;
                            this.lastSaved = data.saved_at;
                            this.updateProgress();
                        }
                    } catch (error) {
                        console.error('Auto-save failed:', error);
                    } finally {
                        this.questionStatus[index].saving = false;
                        this.saving = false;
                    }
                },

                async autoSaveAll() {
                    for (let i = 0; i < this.totalQuestions; i++) {
                        if (this.answers[i] && this.answers[i].trim() !== '') {
                            await this.autoSaveAnswer(i);
                        }
                    }
                },

                confirmSubmit() {
                    // Removed confirm dialogs for better browser compatibility
                    // Auto-save all answers and submit directly
                    this.autoSaveAll().then(() => {
                        document.getElementById('complete-form').submit();
                    });
                },

                updateProgress() {
                    this.answeredCount = this.questionStatus.filter(q => q.answered).length;
                    this.progressPercentage = Math.round((this.answeredCount / this.totalQuestions) * 100);
                },

                saveToLocalStorage() {
                    localStorage.setItem('essay_{{ $content->id }}_answers', JSON.stringify(this.answers));
                },

                loadFromLocalStorage() {
                    const saved = localStorage.getItem('essay_{{ $content->id }}_answers');
                    if (saved) {
                        const savedAnswers = JSON.parse(saved);
                        this.answers = this.answers.map((ans, i) => ans || savedAnswers[i] || '');
                    }
                },

                get answeredCount() {
                    return this.questionStatus.filter(q => q.answered).length;
                },

                get progressPercentage() {
                    return Math.round((this.answeredCount / this.totalQuestions) * 100);
                }
            }
        }
    </script>
</x-app-layout>
