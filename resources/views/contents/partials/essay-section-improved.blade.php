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

        {{-- JIKA BELUM ADA JAWABAN ATAU STATUS DRAFT DAN USER DIIZINKAN MENGERJAKAN --}}
        @elseif (Auth::user()->can('attempt quizzes'))
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
                {{-- NEW QUIZ-STYLE NAVIGATION SYSTEM --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg -mx-8 -mt-6 -mb-6 p-4 lg:p-6">
                    <!-- Split Layout: Sidebar Kiri + Content Kanan - MAXIMIZE WIDTH -->
                    <div class="flex gap-4 lg:gap-6 flex-col lg:flex-row max-w-none">

                        <!-- SIDEBAR KIRI - Daftar Nomor Soal (Sticky pada lg+) - SUPER COMPACT -->
                        <div class="lg:w-64 lg:flex-shrink-0">
                            <div class="lg:sticky lg:top-24 space-y-4">
                                <!-- Progress Card - MORE COMPACT -->
                                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Progress Essay
                                    </h3>

                                    <div class="mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span id="progress-text" class="text-xs font-semibold text-gray-700 dark:text-gray-300">0 / {{ $questions->count() }}</span>
                                            <span id="progress-percentage" class="text-xs font-bold text-indigo-600">0%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div id="progress-bar" class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                                        </div>
                                    </div>

                                    <!-- Auto Save Indicator -->
                                    <div id="save-indicator" class="flex items-center space-x-2 text-green-500 text-xs transition-opacity duration-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="font-medium" id="save-indicator-text">Draft tersimpan otomatis</span>
                                    </div>

                                    <!-- Current Question Info -->
                                    <div class="mt-3 p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg border border-indigo-200 dark:border-indigo-700">
                                        <div class="text-xs text-indigo-700 dark:text-indigo-300">
                                            <span class="font-semibold">Soal Aktif:</span>
                                            <span id="current-question-display" class="ml-2 text-base font-bold">1</span>
                                            <span class="text-gray-600 dark:text-gray-400">/ {{ $questions->count() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Question Navigation - MORE COMPACT -->
                                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                                        <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        Navigasi Soal
                                    </h4>

                                    <!-- Grid 5 Kolom untuk Nomor Soal -->
                                    <div class="grid grid-cols-5 gap-1.5">
                                        @for($i = 1; $i <= $questions->count(); $i++)
                                            <button type="button" onclick="goToQuestion({{ $i - 1 }})"
                                                    class="question-nav-btn w-full aspect-square rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-bold transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:scale-105"
                                                    data-question="{{ $i - 1 }}"
                                                    title="Soal {{ $i }}">
                                                {{ $i }}
                                            </button>
                                        @endfor
                                    </div>

                                    <!-- Legend - MORE COMPACT -->
                                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 space-y-1.5 text-xs">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-5 h-5 rounded border-2 border-gray-300 bg-white dark:bg-gray-800"></div>
                                            <span class="text-gray-600 dark:text-gray-400">Belum dijawab</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-5 h-5 rounded border-2 border-green-400 bg-green-100 dark:bg-green-900/30"></div>
                                            <span class="text-gray-600 dark:text-gray-400">Sudah dijawab</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-5 h-5 rounded border-2 border-indigo-500 bg-indigo-100 dark:bg-indigo-900/30"></div>
                                            <span class="text-gray-600 dark:text-gray-400">Sedang dilihat</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button di Sidebar - MORE COMPACT -->
                                <div class="hidden lg:block">
                                    <button type="button"
                                            onclick="showSubmitConfirmation(event)"
                                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-5 py-3 rounded-xl text-base font-bold shadow-lg transform transition-all duration-300 hover:scale-105">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Kirim Semua Jawaban
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- CONTENT KANAN - Soal Essay (1 per 1) - MAXIMIZE WIDTH -->
                        <div class="flex-1 min-w-0 max-w-none">
                            <form id="essay-form" action="{{ route('essays.store', $content) }}" method="POST">
                                @csrf

                                <!-- Question Container - Hanya tampilkan 1 soal - FULL WIDTH -->
                                <div id="question-container" class="max-w-none">
                                    @foreach($questions as $index => $question)
                                        <div class="question-slide {{ $index === 0 ? 'active' : 'hidden' }}"
                                             data-question-index="{{ $index }}"
                                             data-question-id="{{ $question->id }}"
                                             id="question-slide-{{ $index }}">

                                            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 w-full">
                                                <!-- Question Header - WIDER -->
                                                <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-blue-50 dark:from-indigo-900/30 dark:via-purple-900/30 dark:to-blue-900/30 px-6 lg:px-10 py-5 border-b border-gray-200 dark:border-gray-700">
                                                    <div class="flex items-center justify-between flex-wrap gap-4">
                                                        <div class="flex items-center space-x-4">
                                                            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                                                                {{ $index + 1 }}
                                                            </div>
                                                            <div>
                                                                <span class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                                    Pertanyaan {{ $index + 1 }} dari {{ $questions->count() }}
                                                                </span>
                                                                @if ($content->scoring_enabled)
                                                                    <div class="flex items-center space-x-3 mt-1">
                                                                        <span class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                                            </svg>{{ $question->max_score }} poin
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Question Status -->
                                                        <div class="question-status flex items-center space-x-2">
                                                            <div class="status-indicator w-4 h-4 rounded-full border-2 border-gray-300 transition-all duration-300"></div>
                                                            <span class="status-text text-sm text-gray-500 dark:text-gray-400">Belum dijawab</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Question Content - MAXIMUM WIDTH & HEIGHT -->
                                                <div class="p-6 lg:p-10 bg-white dark:bg-gray-900 min-h-[650px]">
                                                    <div class="mb-6">
                                                        <div class="p-5 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border-l-4 border-indigo-500">
                                                            <p class="text-xl font-medium text-gray-900 dark:text-gray-100 leading-relaxed whitespace-pre-line">{{ $question->question }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="relative">
                                                        <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-3">
                                                            Jawaban Anda:
                                                        </label>
                                                        <textarea
                                                            name="answer_{{ $question->id }}"
                                                            id="answer_{{ $question->id }}"
                                                            data-question-id="{{ $question->id }}"
                                                            class="essay-answer w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-5 py-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-200 resize-y min-h-[500px] text-base leading-relaxed transition-all duration-200"
                                                            placeholder="Tulis jawaban Anda di sini... (draft akan tersimpan otomatis)"
                                                            required>{{ old("answer_{$question->id}") }}</textarea>

                                                        {{-- Character counter --}}
                                                        <div class="flex items-center justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
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

                                                <!-- Navigation Buttons - WIDER -->
                                                <div class="bg-gray-50 dark:bg-gray-800 px-6 lg:px-10 py-5 border-t border-gray-200 dark:border-gray-700">
                                                    <div class="flex items-center justify-between flex-wrap gap-4">
                                                        <button type="button"
                                                                onclick="previousQuestion()"
                                                                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg transition-all duration-200 {{ $index === 0 ? 'invisible' : '' }}"
                                                                id="prev-btn-{{ $index }}">
                                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                            </svg>
                                                            Sebelumnya
                                                        </button>

                                                        <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                                            Soal <span class="text-indigo-600 font-bold">{{ $index + 1 }}</span> dari {{ $questions->count() }}
                                                        </div>

                                                        <button type="button"
                                                                onclick="nextQuestion()"
                                                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 {{ $index === $questions->count() - 1 ? 'hidden' : '' }}"
                                                                id="next-btn-{{ $index }}">
                                                            Selanjutnya
                                                            <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </button>

                                                        <button type="button"
                                                                onclick="showSubmitConfirmation(event)"
                                                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all duration-200 {{ $index !== $questions->count() - 1 ? 'hidden' : '' }}"
                                                                id="submit-btn-{{ $index }}">
                                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Kirim Semua Jawaban
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Modal -->
                <div id="submit-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 shadow-2xl">
                        <div class="text-center">
                            <div class="bg-gradient-to-br from-yellow-100 to-orange-100 dark:from-yellow-900/30 dark:to-orange-900/30 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Konfirmasi Pengiriman</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                                Apakah Anda yakin ingin mengirim semua jawaban essay?
                                <br><strong>Setelah dikirim, Anda tidak dapat mengubah jawaban lagi.</strong>
                            </p>

                            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Pertanyaan Dijawab:</span>
                                        <br><span id="modal-answered-count" class="font-bold text-indigo-600">0 / {{ $questions->count() }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Total Pertanyaan:</span>
                                        <br><span class="font-bold text-green-600">{{ $questions->count() }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex space-x-4">
                                <button type="button"
                                        onclick="hideSubmitConfirmation()"
                                        class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 py-3 px-6 rounded-xl font-semibold transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>Batalkan
                                </button>
                                <button type="button"
                                        onclick="submitEssay()"
                                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>Ya, Kirim
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Custom Styles --}}
                <style>
                    .question-slide {
                        transition: all 0.3s ease-in-out;
                    }

                    .question-slide.hidden {
                        display: none;
                    }

                    .question-slide.active {
                        display: block;
                        animation: fadeIn 0.3s ease-in-out;
                    }

                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                            transform: translateY(10px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }

                    /* Highlight active question in sidebar */
                    .question-nav-btn.active {
                        border-color: #6366f1 !important;
                        background: #eef2ff !important;
                        transform: scale(1.1);
                        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
                    }

                    .question-nav-btn.answered {
                        border-color: #10b981 !important;
                        background: #d1fae5 !important;
                        color: #047857;
                    }

                    /* Dark mode adjustments for navigation buttons */
                    .dark .question-nav-btn.active {
                        background: rgba(99, 102, 241, 0.3) !important;
                    }

                    .dark .question-nav-btn.answered {
                        background: rgba(16, 185, 129, 0.2) !important;
                    }

                    /* Textarea focus effect */
                    .essay-answer:focus {
                        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
                    }
                </style>

                {{-- Enhanced Autosave JavaScript with Navigation --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const contentId = {{ $content->id }};
                        const autosaveUrl = "{{ route('essays.autosave', $content) }}";
                        const getDraftsUrl = "{{ route('essays.get_drafts', $content) }}";
                        const csrfToken = "{{ csrf_token() }}";
                        const totalQuestions = {{ $questions->count() }};

                        let currentQuestionIndex = 0;
                        let autosaveTimers = {};
                        let answeredQuestions = new Set();

                        // Navigation Functions
                        window.goToQuestion = function(index) {
                            // Auto-save current question before switching
                            const currentTextarea = document.querySelector(`.question-slide.active textarea.essay-answer`);
                            if (currentTextarea) {
                                const questionId = currentTextarea.dataset.questionId;
                                autosaveAnswer(questionId, currentTextarea.value, false);
                            }

                            // Hide all questions
                            document.querySelectorAll('.question-slide').forEach(slide => {
                                slide.classList.remove('active');
                                slide.classList.add('hidden');
                            });

                            // Show target question
                            const targetSlide = document.getElementById(`question-slide-${index}`);
                            if (targetSlide) {
                                targetSlide.classList.remove('hidden');
                                targetSlide.classList.add('active');
                                currentQuestionIndex = index;

                                // Update current question display
                                document.getElementById('current-question-display').textContent = index + 1;

                                // Update navigation button active state
                                updateNavigationButtons();

                                // Scroll to top smoothly
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }
                        };

                        window.nextQuestion = function() {
                            if (currentQuestionIndex < totalQuestions - 1) {
                                goToQuestion(currentQuestionIndex + 1);
                            }
                        };

                        window.previousQuestion = function() {
                            if (currentQuestionIndex > 0) {
                                goToQuestion(currentQuestionIndex - 1);
                            }
                        };

                        function updateNavigationButtons() {
                            // Update sidebar navigation buttons
                            document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
                                if (index === currentQuestionIndex) {
                                    btn.classList.add('active');
                                } else {
                                    btn.classList.remove('active');
                                }
                            });
                        }

                        // Load existing drafts
                        loadDrafts();

                        // Setup autosave for all textareas
                        document.querySelectorAll('.essay-answer').forEach(textarea => {
                            const questionId = textarea.dataset.questionId;

                            // Character counter and autosave
                            textarea.addEventListener('input', function() {
                                updateCharCount(this);
                                updateProgress();
                                updateQuestionStatus(this);

                                // Autosave with debounce
                                clearTimeout(autosaveTimers[questionId]);
                                autosaveTimers[questionId] = setTimeout(() => {
                                    autosaveAnswer(questionId, this.value, true);
                                }, 2000); // Save 2 seconds after stop typing
                            });

                            // Initial char count
                            updateCharCount(textarea);
                        });

                        // Form submission
                        document.getElementById('essay-form').addEventListener('submit', function(e) {
                            e.preventDefault();
                            showSubmitConfirmation();
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
                                            updateQuestionStatus(textarea);
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

                        function autosaveAnswer(questionId, answer, showIndicator = true) {
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
                                    if (showIndicator) {
                                        updateAutosaveIndicator('saved', 'Draft tersimpan ' + data.saved_at);
                                    }

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
                            const counterEl = textarea.closest('.question-slide').querySelector('.char-count');
                            if (counterEl) {
                                counterEl.textContent = count.toLocaleString() + ' karakter';
                            }
                        }

                        function updateQuestionStatus(textarea) {
                            const questionSlide = textarea.closest('.question-slide');
                            if (!questionSlide) return;

                            const statusIndicator = questionSlide.querySelector('.status-indicator');
                            const statusText = questionSlide.querySelector('.status-text');
                            const hasAnswer = textarea.value.trim().length > 0;

                            const questionIndex = parseInt(questionSlide.dataset.questionIndex);
                            const questionId = questionSlide.dataset.questionId;

                            if (hasAnswer) {
                                statusIndicator.className = 'status-indicator w-4 h-4 rounded-full bg-green-500 transition-all duration-300';
                                statusText.textContent = 'Sudah dijawab';
                                statusText.className = 'status-text text-sm text-green-600 font-medium';

                                // Update nav button
                                const navBtn = document.querySelector(`[data-question="${questionIndex}"]`);
                                if (navBtn) {
                                    navBtn.classList.add('answered');
                                }
                            } else {
                                statusIndicator.className = 'status-indicator w-4 h-4 rounded-full border-2 border-gray-300 transition-all duration-300';
                                statusText.textContent = 'Belum dijawab';
                                statusText.className = 'status-text text-sm text-gray-500';

                                // Update nav button
                                const navBtn = document.querySelector(`[data-question="${questionIndex}"]`);
                                if (navBtn) {
                                    navBtn.classList.remove('answered');
                                }
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
                            const indicator = document.getElementById('save-indicator');
                            const textEl = document.getElementById('save-indicator-text');

                            if (status === 'saved') {
                                indicator.className = 'flex items-center space-x-2 text-green-600 transition-opacity duration-300';
                            } else if (status === 'saving') {
                                indicator.className = 'flex items-center space-x-2 text-yellow-600 transition-opacity duration-300';
                            }

                            textEl.textContent = text;
                        }

                        function updateProgress() {
                            // Count how many questions have answers
                            let answeredCount = 0;
                            document.querySelectorAll('.essay-answer').forEach(textarea => {
                                if (textarea.value.trim().length > 0) {
                                    answeredCount++;
                                }
                            });

                            const percentage = (answeredCount / totalQuestions) * 100;

                            document.getElementById('progress-bar').style.width = percentage + '%';
                            document.getElementById('progress-text').textContent = `${answeredCount} / ${totalQuestions}`;
                            document.getElementById('progress-percentage').textContent = Math.round(percentage) + '%';

                            const modalAnsweredCount = document.getElementById('modal-answered-count');
                            if (modalAnsweredCount) {
                                modalAnsweredCount.textContent = `${answeredCount} / ${totalQuestions}`;
                            }
                        }

                        // Submit Modal Functions
                        window.showSubmitConfirmation = function(event) {
                            if (event) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            updateProgress();

                            const modal = document.getElementById('submit-modal');
                            modal.classList.remove('hidden');
                            modal.classList.add('flex');

                            const modalContent = modal.querySelector('div > div');
                            modalContent.style.transform = 'scale(0.8)';
                            modalContent.style.opacity = '0';

                            setTimeout(() => {
                                modalContent.style.transform = 'scale(1)';
                                modalContent.style.opacity = '1';
                            }, 50);
                        };

                        window.hideSubmitConfirmation = function() {
                            const modal = document.getElementById('submit-modal');
                            const modalContent = modal.querySelector('div > div');

                            modalContent.style.transform = 'scale(0.8)';
                            modalContent.style.opacity = '0';

                            setTimeout(() => {
                                modal.classList.add('hidden');
                                modal.classList.remove('flex');
                            }, 200);
                        };

                        window.submitEssay = function() {
                            const submitBtn = event.target;
                            submitBtn.innerHTML = '<svg class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Mengirim...';
                            submitBtn.disabled = true;

                            hideSubmitConfirmation();

                            // Submit the form
                            setTimeout(() => {
                                document.getElementById('essay-form').submit();
                            }, 300);
                        };

                        // Initialize
                        updateProgress();
                        updateNavigationButtons();

                        // Keyboard navigation
                        document.addEventListener('keydown', function(e) {
                            // Only if not typing in textarea
                            if (document.activeElement.tagName === 'TEXTAREA') {
                                return;
                            }

                            if (e.key === 'ArrowRight') {
                                nextQuestion();
                            } else if (e.key === 'ArrowLeft') {
                                previousQuestion();
                            }
                        });

                        // Periodic autosave (every 30 seconds)
                        setInterval(() => {
                            const currentTextarea = document.querySelector(`.question-slide.active textarea.essay-answer`);
                            if (currentTextarea && currentTextarea.value.trim().length > 0) {
                                const questionId = currentTextarea.dataset.questionId;
                                autosaveAnswer(questionId, currentTextarea.value, false);
                            }
                        }, 30000);
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
