@if ($content->type == 'essay')
    @php
        $submission = $content->essaySubmissions()->where('user_id', Auth::id())->first();
        $questions = $content->essayQuestions()->orderBy('order')->get();
    @endphp

    <div class="mt-6 border-t pt-6">
        {{-- JIKA SUDAH ADA JAWABAN YANG SUBMITTED --}}
        @if ($submission && $submission->status === 'submitted')
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md dark:bg-green-900 dark:text-green-200 dark:border-green-600" role="alert">
                <p class="font-bold">Anda Sudah Mengumpulkan Jawaban</p>
                <p>Jawaban Anda dikumpulkan pada: {{ $submission->created_at->format('d F Y, H:i') }}</p>

                @php
                    // Logic untuk cek apakah essay sudah diproses
                    $isProcessed = false;

                    // FITUR BARU: Jika essay tidak perlu review (latihan mandiri), langsung dianggap sudah diproses
                    if (!($content->requires_review ?? true)) {
                        $isProcessed = true; // Auto-processed tanpa review instruktur
                    } else {
                        if ($submission->content->scoring_enabled) {
                            // Dengan scoring
                            if ($submission->content->grading_mode === 'overall') {
                                $isProcessed = $submission->answers()->whereNotNull('score')->count() > 0;
                            } else {
                                $isProcessed = $submission->is_fully_graded;
                            }
                        } else {
                            // Tanpa scoring
                            if ($submission->content->grading_mode === 'overall') {
                                $isProcessed = $submission->answers()->whereNotNull('feedback')->count() > 0;
                            } else {
                                $totalQuestions = $submission->content->essayQuestions()->count();
                                $answersWithFeedback = $submission->answers()->whereNotNull('feedback')->count();
                                $isProcessed = $answersWithFeedback >= $totalQuestions;
                            }
                        }
                    }
                @endphp

                @if ($isProcessed)
                    <div class="mt-4">
                        <a href="{{ route('essays.result', $submission->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md inline-block">
                            @if($submission->content->scoring_enabled)
                                Lihat Nilai dan Feedback
                            @else
                                Lihat Hasil dan Feedback
                            @endif
                        </a>
                        @if($submission->content->scoring_enabled)
                            <div class="mt-2">
                                <span class="text-sm">
                                    @if($submission->content->grading_mode === 'overall')
                                        Total Nilai: {{ $submission->answers()->whereNotNull('score')->first()->score ?? 0 }}/{{ $submission->max_total_score }}
                                    @else
                                        Total Nilai: {{ $submission->total_score }}/{{ $submission->max_total_score }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                @else
                    @if(!($submission->content->requires_review ?? true))
                        {{-- Essay latihan mandiri - tidak perlu review --}}
                        <div class="mt-4">
                            <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Latihan Selesai
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Essay latihan mandiri telah berhasil dikumpulkan dan langsung selesai.</p>
                            <div class="mt-3">
                                <a href="{{ route('essays.result', $submission->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md inline-block">
                                    Lihat Jawaban Saya
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- Essay yang masih menunggu review --}}
                        <p class="mt-2">
                            @if($submission->content->scoring_enabled)
                                Jawaban Anda sedang menunggu penilaian dari instruktur.
                            @else
                                Jawaban Anda sedang menunggu tinjauan dari instruktur.
                            @endif
                        </p>
                    @endif
                @endif
            </div>

        {{-- JIKA BELUM ADA JAWABAN ATAU STATUS DRAFT DAN USER ADALAH PESERTA --}}
        @elseif (Auth::user()->hasRole('participant'))
            @if ($questions->isEmpty())
                {{-- Fallback untuk essay lama tanpa questions --}}
                <form action="{{ route('essays.store', $content) }}" method="POST" class="mt-6">
                    @csrf
                    <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Tulis Jawaban Anda:</h3>

                    <x-forms.summernote-editor id="essay_editor" name="essay_content" />

                    <div class="mt-4">
                        <x-primary-button>{{ __('Kirim Jawaban') }}</x-primary-button>
                    </div>
                </form>
            @else
                {{-- NEW IMPROVED SYSTEM: Multiple questions with autosave --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Essay Questions</h3>
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $questions->count() }} {{ Str::plural('pertanyaan', $questions->count()) }}
                                @if ($content->scoring_enabled)
                                    | Total: {{ $questions->sum('max_score') }} poin
                                @endif
                            </p>

                            {{-- Autosave indicator --}}
                            <div id="autosave-indicator" class="text-sm text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span id="autosave-text">Draft tersimpan otomatis</span>
                            </div>
                        </div>

                        {{-- Progress bar --}}
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Progress Pengerjaan</span>
                                <span id="progress-text" class="text-xs font-semibold text-gray-700 dark:text-gray-300">0 / {{ $questions->count() }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progress-bar" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <form id="essay-form" action="{{ route('essays.store', $content) }}" method="POST" class="p-6">
                        @csrf

                        <div class="space-y-6" id="questions-container">
                            @foreach($questions as $index => $question)
                                <div class="question-card border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-gray-50 dark:bg-gray-900 transition-all hover:shadow-md" data-question-id="{{ $question->id }}">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold">
                                                    {{ $index + 1 }}
                                                </span>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                    Pertanyaan {{ $index + 1 }}
                                                </h4>
                                            </div>
                                        </div>
                                        @if ($content->scoring_enabled)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                </svg>
                                                {{ $question->max_score }} poin
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg border-l-4 border-indigo-500">
                                        <p class="text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-line">{{ $question->question }}</p>
                                    </div>

                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Jawaban Anda:
                                        </label>
                                        <textarea
                                            name="answer_{{ $question->id }}"
                                            id="answer_{{ $question->id }}"
                                            data-question-id="{{ $question->id }}"
                                            class="essay-answer w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-200 resize-y min-h-[200px]"
                                            placeholder="Tulis jawaban Anda di sini... (draft akan tersimpan otomatis)"
                                            required>{{ old("answer_{$question->id}") }}</textarea>

                                        {{-- Character counter --}}
                                        <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                                            <span class="char-count">0 karakter</span>
                                            <span class="save-status" data-question-id="{{ $question->id }}">
                                                <span class="text-gray-400">Belum tersimpan</span>
                                            </span>
                                        </div>

                                        @error("answer_{$question->id}")
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex items-center justify-between p-6 bg-gray-100 dark:bg-gray-800 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <p class="font-medium">Pastikan semua pertanyaan telah dijawab</p>
                                <p class="text-xs mt-1">Draft tersimpan otomatis setiap Anda mengetik</p>
                            </div>
                            <button type="submit" id="submit-btn" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Kirim Semua Jawaban') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Autosave JavaScript --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const contentId = {{ $content->id }};
                        const autosaveUrl = "{{ route('essays.autosave', $content) }}";
                        const getDraftsUrl = "{{ route('essays.get_drafts', $content) }}";
                        const csrfToken = "{{ csrf_token() }}";

                        let autosaveTimers = {};
                        let answeredQuestions = new Set();

                        // Load existing drafts
                        loadDrafts();

                        // Setup autosave for all textareas
                        document.querySelectorAll('.essay-answer').forEach(textarea => {
                            const questionId = textarea.dataset.questionId;

                            // Character counter
                            textarea.addEventListener('input', function() {
                                updateCharCount(this);
                                updateProgress();

                                // Autosave with debounce
                                clearTimeout(autosaveTimers[questionId]);
                                autosaveTimers[questionId] = setTimeout(() => {
                                    autosaveAnswer(questionId, this.value);
                                }, 2000); // Save 2 seconds after stop typing
                            });

                            // Initial char count
                            updateCharCount(textarea);
                        });

                        // Form submission
                        document.getElementById('essay-form').addEventListener('submit', function(e) {
                            const submitBtn = document.getElementById('submit-btn');
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Mengirim...';
                        });

                        function loadDrafts() {
                            fetch(getDraftsUrl, {
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.drafts) {
                                    Object.keys(data.drafts).forEach(questionId => {
                                        const textarea = document.getElementById(`answer_${questionId}`);
                                        if (textarea && data.drafts[questionId]) {
                                            textarea.value = data.drafts[questionId];
                                            updateCharCount(textarea);
                                            if (data.drafts[questionId].trim().length > 0) {
                                                answeredQuestions.add(questionId);
                                                updateSaveStatus(questionId, 'saved', 'Draft tersimpan');
                                            }
                                        }
                                    });
                                    updateProgress();
                                }
                            })
                            .catch(error => console.error('Error loading drafts:', error));
                        }

                        function autosaveAnswer(questionId, answer) {
                            updateSaveStatus(questionId, 'saving', 'Menyimpan...');

                            fetch(autosaveUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    question_id: questionId,
                                    answer: answer
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    updateSaveStatus(questionId, 'saved', 'Tersimpan ' + data.saved_at);
                                    updateAutosaveIndicator('saved', 'Draft tersimpan ' + data.saved_at);

                                    if (answer.trim().length > 0) {
                                        answeredQuestions.add(questionId);
                                    } else {
                                        answeredQuestions.delete(questionId);
                                    }
                                    updateProgress();
                                } else {
                                    updateSaveStatus(questionId, 'error', 'Gagal menyimpan');
                                }
                            })
                            .catch(error => {
                                console.error('Autosave error:', error);
                                updateSaveStatus(questionId, 'error', 'Gagal menyimpan');
                            });
                        }

                        function updateCharCount(textarea) {
                            const count = textarea.value.length;
                            const counterEl = textarea.closest('.question-card').querySelector('.char-count');
                            if (counterEl) {
                                counterEl.textContent = count.toLocaleString() + ' karakter';
                            }
                        }

                        function updateSaveStatus(questionId, status, text) {
                            const statusEl = document.querySelector(`.save-status[data-question-id="${questionId}"]`);
                            if (!statusEl) return;

                            const colors = {
                                'saving': 'text-yellow-600',
                                'saved': 'text-green-600',
                                'error': 'text-red-600'
                            };

                            statusEl.className = 'save-status ' + colors[status];
                            statusEl.innerHTML = `<span>${text}</span>`;
                        }

                        function updateAutosaveIndicator(status, text) {
                            const indicator = document.getElementById('autosave-indicator');
                            const textEl = document.getElementById('autosave-text');

                            if (status === 'saved') {
                                indicator.className = 'text-sm text-green-600 flex items-center';
                            } else if (status === 'saving') {
                                indicator.className = 'text-sm text-yellow-600 flex items-center';
                            }

                            textEl.textContent = text;
                        }

                        function updateProgress() {
                            const totalQuestions = {{ $questions->count() }};
                            const answeredCount = answeredQuestions.size;
                            const percentage = (answeredCount / totalQuestions) * 100;

                            document.getElementById('progress-bar').style.width = percentage + '%';
                            document.getElementById('progress-text').textContent = `${answeredCount} / ${totalQuestions}`;
                        }

                        // Warn before leaving if there are unsaved changes
                        let hasUnsavedChanges = false;
                        document.querySelectorAll('.essay-answer').forEach(textarea => {
                            textarea.addEventListener('input', () => {
                                hasUnsavedChanges = true;
                            });
                        });

                        // Clear warning after successful autosave
                        setInterval(() => {
                            hasUnsavedChanges = false;
                        }, 5000);
                    });
                </script>
            @endif

        {{-- JIKA USER BISA EDIT CONTENT (instructor/admin) --}}
        @elseif (Auth::user()->can('update', $content->lesson->course))
            <div class="mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Kelola Pertanyaan Essay</h3>
                    {{-- Status scoring indicator --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Status Penilaian:</span>
                        @if ($content->scoring_enabled)
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Dengan Penilaian
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tanpa Penilaian
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Form tambah pertanyaan --}}
                <form action="{{ route('essay.questions.store', $content) }}" method="POST" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    @csrf
                    <div class="mb-4">
                        <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                            Pertanyaan Baru:
                        </label>
                        <textarea
                            id="question"
                            name="question"
                            rows="4"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Masukkan pertanyaan essay..."
                            required
                        ></textarea>
                    </div>

                    {{-- Max score input hanya tampil jika scoring enabled --}}
                    @if ($content->scoring_enabled)
                        <div class="mb-4">
                            <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">
                                Skor Maksimal:
                            </label>
                            <input
                                type="number"
                                id="max_score"
                                name="max_score"
                                min="1"
                                value="100"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            >
                        </div>
                    @else
                        <input type="hidden" name="max_score" value="0">
                    @endif

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors">
                        Tambah Pertanyaan
                    </button>
                </form>

                {{-- Daftar pertanyaan existing --}}
                @if($questions->count() > 0)
                    <div class="space-y-4" id="questions-list">
                        @foreach($questions as $index => $question)
                            <div class="bg-white border border-gray-200 rounded-lg p-4" id="question-{{ $question->id }}">
                                {{-- View Mode --}}
                                <div class="view-mode">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-700">Pertanyaan {{ $index + 1 }}</h4>
                                            <p class="text-gray-600 mt-1 whitespace-pre-line">{{ $question->question }}</p>
                                            {{-- Tampilkan max score hanya jika scoring enabled --}}
                                            @if ($content->scoring_enabled)
                                                <span class="text-sm text-gray-500 mt-2 inline-block">Skor Maksimal: {{ $question->max_score }} poin</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            <button type="button"
                                                    class="edit-question-btn text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50 transition-colors"
                                                    data-question-id="{{ $question->id }}"
                                                    title="Edit pertanyaan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <form action="{{ route('essay.questions.destroy', $question) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                        onclick="return confirm('Hapus pertanyaan ini?')"
                                                        title="Hapus pertanyaan">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Edit Mode (Initially Hidden) --}}
                                <div class="edit-mode hidden">
                                    <form action="{{ route('essay.questions.update', $question) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Edit Pertanyaan {{ $index + 1 }}:
                                                </label>
                                                <textarea
                                                    name="question"
                                                    rows="4"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    required>{{ $question->question }}</textarea>
                                            </div>

                                            @if ($content->scoring_enabled)
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Skor Maksimal:
                                                    </label>
                                                    <input
                                                        type="number"
                                                        name="max_score"
                                                        min="1"
                                                        value="{{ $question->max_score }}"
                                                        class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                        required>
                                                </div>
                                            @else
                                                <input type="hidden" name="max_score" value="0">
                                            @endif

                                            <div class="flex items-center gap-2">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Simpan Perubahan
                                                </button>
                                                <button type="button"
                                                        class="cancel-edit-btn inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-md transition-colors"
                                                        data-question-id="{{ $question->id }}">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- JavaScript for Edit Toggle --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Edit button handlers
                            document.querySelectorAll('.edit-question-btn').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    const questionId = this.dataset.questionId;
                                    const container = document.getElementById(`question-${questionId}`);
                                    container.querySelector('.view-mode').classList.add('hidden');
                                    container.querySelector('.edit-mode').classList.remove('hidden');
                                });
                            });

                            // Cancel button handlers
                            document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    const questionId = this.dataset.questionId;
                                    const container = document.getElementById(`question-${questionId}`);
                                    container.querySelector('.view-mode').classList.remove('hidden');
                                    container.querySelector('.edit-mode').classList.add('hidden');
                                });
                            });
                        });
                    </script>
                @endif
            </div>
        @endif
    </div>
@endif
