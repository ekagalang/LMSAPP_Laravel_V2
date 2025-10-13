<x-app-layout>
    <x-slot name="header">
        <!-- Compact Quiz Header -->
        <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 text-white -mx-6 -mt-6 mb-6 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full bg-pattern"></div>
            </div>

            <div class="relative px-6 py-6">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between">
                        <!-- Quiz Info -->
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl border border-white/30 shadow-lg">
                                <i class="fas fa-brain text-2xl text-yellow-300"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">{{ $quiz->title }}</h1>
                                <p class="text-blue-100 text-sm">
                                    @if ($quiz->lesson && $quiz->lesson->course)
                                        {{ $quiz->lesson->course->title }}
                                    @else
                                        Kursus Tidak Ditemukan
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Quiz Stats -->
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2 border border-white/20">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-white">{{ $quiz->questions->count() }}</div>
                                    <div class="text-xs text-blue-200">Soal</div>
                                </div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2 border border-white/20">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-white">{{ $quiz->pass_marks }}</div>
                                    <div class="text-xs text-blue-200">Nilai Lulus</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Floating Timer (Pojok Kanan Atas) -->
    @if ($quiz->time_limit)
        <div id="floating-timer" class="fixed top-20 right-6 z-50 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-2xl p-4 border-2 border-white/20 backdrop-blur-lg transform hover:scale-105 transition-all duration-300">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-2 mb-2">
                    <i class="fas fa-stopwatch text-yellow-300 text-sm"></i>
                    <span class="text-xs font-semibold text-white">Sisa Waktu</span>
                </div>

                <!-- Circular Timer -->
                <div class="relative w-20 h-20 mx-auto mb-2">
                    <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42" stroke="rgba(255,255,255,0.2)" stroke-width="6" fill="none"/>
                        <circle id="timer-circle" cx="50" cy="50" r="42" stroke="url(#gradient)" stroke-width="6" fill="none"
                                stroke-linecap="round" stroke-dasharray="264" stroke-dashoffset="0"
                                class="transition-all duration-1000"/>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#fbbf24"/>
                                <stop offset="100%" style="stop-color:#f59e0b"/>
                            </linearGradient>
                        </defs>
                    </svg>

                    <div class="absolute inset-0 flex items-center justify-center">
                        <div id="time-left" class="text-xl font-bold font-mono text-white tracking-tight"></div>
                    </div>
                </div>

                <!-- Timer Status -->
                <div id="timer-status" class="text-xs font-medium text-blue-100">
                    <i class="fas fa-play text-green-400 mr-1"></i>
                    In Progress
                </div>
            </div>
        </div>
    @endif

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Split Layout: Sidebar Kiri + Content Kanan -->
            <div class="flex gap-6">

                <!-- SIDEBAR KIRI - Daftar Nomor Soal (Sticky) -->
                <div class="w-80 flex-shrink-0">
                    <div class="sticky top-24">
                        <!-- Progress Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-tasks text-indigo-600 mr-2"></i>
                                Progress Quiz
                            </h3>

                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span id="progress-text" class="text-sm font-semibold text-gray-700">0 / {{ $quiz->questions->count() }}</span>
                                    <span id="progress-percentage" class="text-sm font-bold text-indigo-600">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div id="progress-bar" class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Auto Save Indicator -->
                            <div id="save-indicator" class="flex items-center space-x-2 text-green-500 text-sm opacity-0 transition-opacity duration-300 mb-4">
                                <i class="fas fa-check-circle"></i>
                                <span class="font-medium">Auto-saved</span>
                            </div>

                            <!-- Current Question Info -->
                            <div class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                                <div class="text-sm text-indigo-700">
                                    <span class="font-semibold">Soal Aktif:</span>
                                    <span id="current-question-display" class="ml-2 text-lg font-bold">1</span>
                                    <span class="text-gray-600">/ {{ $quiz->questions->count() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Question Navigation -->
                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                            <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-list-ol text-indigo-600 mr-2"></i>
                                Navigasi Soal
                            </h4>

                            <!-- Grid 5 Kolom untuk Nomor Soal -->
                            <div class="grid grid-cols-5 gap-2">
                                @for($i = 1; $i <= $quiz->questions->count(); $i++)
                                    <button onclick="goToQuestion({{ $i - 1 }})"
                                            class="question-nav-btn w-full aspect-square rounded-lg border-2 border-gray-300 bg-white text-sm font-bold transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-50 hover:scale-105"
                                            data-question="{{ $i - 1 }}"
                                            title="Soal {{ $i }}">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>

                            <!-- Legend -->
                            <div class="mt-6 pt-4 border-t border-gray-200 space-y-2 text-sm">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded border-2 border-gray-300 bg-white"></div>
                                    <span class="text-gray-600">Belum dijawab</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded border-2 border-green-400 bg-green-100"></div>
                                    <span class="text-gray-600">Sudah dijawab</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded border-2 border-indigo-500 bg-indigo-100"></div>
                                    <span class="text-gray-600">Sedang dilihat</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button di Sidebar -->
                        <div class="mt-6">
                            <button type="button"
                                    onclick="showSubmitConfirmation(event)"
                                    class="w-full btn-submit bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-4 rounded-xl text-lg font-bold shadow-lg transform transition-all duration-300 hover:scale-105">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Kirim Jawaban
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CONTENT KANAN - Soal Quiz (1 per 1) -->
                <div class="flex-1">
                    <form id="quiz-attempt-form" method="POST" action="{{ route('quizzes.submit_attempt', [$quiz, $attempt]) }}">
                        @csrf

                        <!-- Question Container - Hanya tampilkan 1 soal -->
                        <div id="question-container">
                            @foreach ($quiz->questions as $index => $question)
                                <div class="question-slide {{ $index === 0 ? 'active' : 'hidden' }}"
                                     data-question-index="{{ $index }}"
                                     id="question-slide-{{ $index }}">

                                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                                        <!-- Question Header -->
                                        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-blue-50 px-8 py-5 border-b border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div>
                                                        <span class="text-lg font-semibold text-gray-800">
                                                            Soal {{ $index + 1 }} dari {{ $quiz->questions->count() }}
                                                        </span>
                                                        <div class="flex items-center space-x-3 mt-1">
                                                            <span class="text-sm text-gray-600">
                                                                @if($question->type === 'multiple_choice')
                                                                    <i class="fas fa-list-ul mr-1"></i>Pilihan Ganda
                                                                @else
                                                                    <i class="fas fa-check-double mr-1"></i>Benar/Salah
                                                                @endif
                                                            </span>
                                                            <span class="text-sm text-indigo-600 font-medium">
                                                                <i class="fas fa-star mr-1"></i>{{ $question->marks }} poin
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Question Status -->
                                                <div class="question-status flex items-center space-x-2">
                                                    <div class="status-indicator w-4 h-4 rounded-full border-2 border-gray-300 transition-all duration-300"></div>
                                                    <span class="status-text text-sm text-gray-500">Belum dijawab</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Question Content -->
                                        <div class="p-8 bg-white min-h-[400px]">
                                            <div class="mb-8">
                                                <div class="prose prose-lg max-w-none">
                                                    <p class="text-xl font-medium text-gray-900 leading-relaxed mb-6">
                                                        {{ $question->question_text }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Multiple Choice Options -->
                                            @if ($question->type === 'multiple_choice')
                                                <div class="space-y-4">
                                                    @foreach ($question->options as $optionIndex => $option)
                                                        <label class="option-item group block relative cursor-pointer">
                                                            <input type="radio"
                                                                   name="answers[{{ $index }}][option_id]"
                                                                   value="{{ $option->id }}"
                                                                   class="sr-only peer"
                                                                   onchange="updateProgress(); autoSave(); updateQuestionStatus({{ $index }});">

                                                            <div class="bg-white border-2 border-gray-200 rounded-xl p-6 transition-all duration-300 hover:border-indigo-300 hover:shadow-lg hover:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:shadow-lg">
                                                                <div class="flex items-center space-x-4">
                                                                    <!-- Custom Radio -->
                                                                    <div class="relative">
                                                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 bg-white transition-all duration-200 peer-checked:border-indigo-500 group-hover:border-indigo-400"></div>
                                                                        <div class="absolute inset-0 flex items-center justify-center">
                                                                            <div class="w-3 h-3 rounded-full bg-indigo-500 scale-0 transition-transform duration-200 peer-checked:scale-100"></div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Option Letter -->
                                                                    <div class="bg-gray-100 text-gray-700 w-10 h-10 rounded-lg flex items-center justify-center font-bold text-lg transition-all duration-200 peer-checked:bg-indigo-100 peer-checked:text-indigo-700">
                                                                        {{ chr(65 + $optionIndex) }}
                                                                    </div>

                                                                    <!-- Option Text -->
                                                                    <div class="flex-1">
                                                                        <span class="text-lg font-medium text-gray-800 transition-colors duration-200 peer-checked:text-indigo-800">
                                                                            {{ $option->option_text }}
                                                                        </span>
                                                                    </div>

                                                                    <!-- Selection Indicator -->
                                                                    <div class="opacity-0 transition-opacity duration-200 peer-checked:opacity-100">
                                                                        <i class="fas fa-check-circle text-indigo-500 text-xl"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">

                                            <!-- True/False Options -->
                                            @elseif ($question->type === 'true_false')
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- True Option -->
                                                    <label class="option-item group block relative cursor-pointer">
                                                        <input type="radio"
                                                               name="answers[{{ $index }}][answer_text]"
                                                               value="True"
                                                               class="sr-only peer"
                                                               onchange="updateProgress(); autoSave(); updateQuestionStatus({{ $index }});">

                                                        <div class="bg-white border-2 border-green-200 rounded-xl p-8 transition-all duration-300 hover:border-green-400 hover:shadow-lg hover:bg-green-50 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-lg relative h-full">
                                                            <div class="absolute top-4 right-4 opacity-0 peer-checked:opacity-100 transition-all duration-300 transform scale-0 peer-checked:scale-100">
                                                                <div class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg">
                                                                    <i class="fas fa-check text-sm"></i>
                                                                </div>
                                                            </div>

                                                            <div class="text-center">
                                                                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 transition-all duration-200 peer-checked:bg-green-200">
                                                                    <i class="fas fa-check text-green-600 text-2xl"></i>
                                                                </div>
                                                                <h4 class="text-xl font-bold text-green-800 mb-2">BENAR</h4>
                                                                <p class="text-green-700 font-medium">True</p>
                                                            </div>
                                                        </div>
                                                    </label>

                                                    <!-- False Option -->
                                                    <label class="option-item group block relative cursor-pointer">
                                                        <input type="radio"
                                                               name="answers[{{ $index }}][answer_text]"
                                                               value="False"
                                                               class="sr-only peer"
                                                               onchange="updateProgress(); autoSave(); updateQuestionStatus({{ $index }});">

                                                        <div class="bg-white border-2 border-red-200 rounded-xl p-8 transition-all duration-300 hover:border-red-400 hover:shadow-lg hover:bg-red-50 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-lg relative h-full">
                                                            <div class="absolute top-4 right-4 opacity-0 peer-checked:opacity-100 transition-all duration-300 transform scale-0 peer-checked:scale-100">
                                                                <div class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg">
                                                                    <i class="fas fa-check text-sm"></i>
                                                                </div>
                                                            </div>

                                                            <div class="text-center">
                                                                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 transition-all duration-200 peer-checked:bg-red-200">
                                                                    <i class="fas fa-times text-red-600 text-2xl"></i>
                                                                </div>
                                                                <h4 class="text-xl font-bold text-red-800 mb-2">SALAH</h4>
                                                                <p class="text-red-700 font-medium">False</p>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                                <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">
                                            @endif
                                        </div>

                                        <!-- Navigation Buttons -->
                                        <div class="bg-gray-50 px-8 py-5 border-t border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <button type="button"
                                                        onclick="previousQuestion()"
                                                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-all duration-200 {{ $index === 0 ? 'invisible' : '' }}"
                                                        id="prev-btn-{{ $index }}">
                                                    <i class="fas fa-chevron-left mr-2"></i>
                                                    Sebelumnya
                                                </button>

                                                <div class="text-sm text-gray-600 font-medium">
                                                    Soal <span class="text-indigo-600 font-bold">{{ $index + 1 }}</span> dari {{ $quiz->questions->count() }}
                                                </div>

                                                <button type="button"
                                                        onclick="nextQuestion()"
                                                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 {{ $index === $quiz->questions->count() - 1 ? 'hidden' : '' }}"
                                                        id="next-btn-{{ $index }}">
                                                    Selanjutnya
                                                    <i class="fas fa-chevron-right ml-2"></i>
                                                </button>

                                                <button type="button"
                                                        onclick="showSubmitConfirmation(event)"
                                                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all duration-200 {{ $index !== $quiz->questions->count() - 1 ? 'hidden' : '' }}"
                                                        id="submit-btn-{{ $index }}">
                                                    <i class="fas fa-paper-plane mr-2"></i>
                                                    Kirim Jawaban
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
    </div>

    <!-- Confirmation Modal -->
    <div id="submit-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 shadow-2xl">
            <div class="text-center">
                <div class="bg-gradient-to-br from-yellow-100 to-orange-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
                </div>

                <h3 class="text-2xl font-bold text-gray-900 mb-3">Konfirmasi Pengiriman</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Apakah Anda yakin ingin mengirim jawaban?
                    <br><strong>Setelah dikirim, Anda tidak dapat mengubah jawaban lagi.</strong>
                </p>

                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Soal Dijawab:</span>
                            <br><span id="modal-answered-count" class="font-bold text-indigo-600">0 / {{ $quiz->questions->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Sisa Waktu:</span>
                            <br><span id="modal-time-left" class="font-bold text-green-600">--:--</span>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="button"
                            onclick="hideSubmitConfirmation()"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-6 rounded-xl font-semibold transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Batalkan
                    </button>
                    <button type="button"
                            onclick="submitQuiz()"
                            class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-check mr-2"></i>Ya, Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .bg-pattern {
            background-image:
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 2px, transparent 2px);
            background-size: 50px 50px;
        }

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

        .option-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .option-item:hover .peer:not(:checked) ~ div {
            border-color: #818cf8 !important;
            background-color: #eef2ff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15) !important;
        }

        .option-item .peer:checked ~ div {
            transform: translateY(-1px);
            border-width: 3px !important;
        }

        .option-item .peer:checked ~ div .bg-gray-100 {
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
            color: white !important;
            transform: scale(1.1);
        }

        .btn-submit {
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .option-item .peer:checked ~ .bg-white.border-green-200 {
            background: #dcfce7 !important;
            border-color: #16a34a !important;
            border-width: 3px !important;
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.3) !important;
        }

        .option-item .peer:checked ~ .bg-white.border-red-200 {
            background: #fecaca !important;
            border-color: #dc2626 !important;
            border-width: 3px !important;
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3) !important;
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

        /* Floating timer responsive */
        @media (max-width: 1024px) {
            #floating-timer {
                top: 10px;
                right: 10px;
                padding: 12px;
            }
            #floating-timer .w-20 {
                width: 60px;
                height: 60px;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        let currentQuestionIndex = 0;
        const totalQuestions = {{ $quiz->questions->count() }};

        @if ($quiz->time_limit && isset($timeRemaining) && $timeRemaining > 0)
            let timeLeft = Math.floor({{ $timeRemaining }});
            const totalTime = {{ $quiz->time_limit }} * 60;
            const circumference = 2 * Math.PI * 42;

            function updateTimerDisplay() {
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    showTimeUpAlert();
                    return;
                }

                const totalSeconds = Math.floor(timeLeft);
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;

                const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                document.getElementById('time-left').textContent = formattedTime;

                const modalTimeEl = document.getElementById('modal-time-left');
                if (modalTimeEl) {
                    modalTimeEl.textContent = formattedTime;
                }

                const progress = (totalTime - totalSeconds) / totalTime;
                const offset = circumference * progress;
                const circle = document.getElementById('timer-circle');
                if (circle) {
                    circle.style.strokeDashoffset = offset;
                }

                updateTimerStatus(totalSeconds);

                timeLeft = Math.max(0, timeLeft - 1);
            }

            function updateTimerStatus(seconds) {
                const statusEl = document.getElementById('timer-status');
                const timerEl = document.getElementById('time-left');

                if (seconds <= 300) {
                    statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-400 mr-1 animate-pulse"></i>Hampir Habis!';
                    timerEl.style.color = '#fbbf24';
                } else if (seconds <= 600) {
                    statusEl.innerHTML = '<i class="fas fa-clock text-orange-400 mr-1"></i>Perhatikan Waktu';
                } else {
                    statusEl.innerHTML = '<i class="fas fa-play text-green-400 mr-1"></i>In Progress';
                }
            }

            function showTimeUpAlert() {
                // Auto-submit directly without alert for better browser compatibility
                autoSubmitQuiz();
            }

            const timerInterval = setInterval(updateTimerDisplay, 1000);
            updateTimerDisplay();
        @endif

        const quizId = {{ $quiz->id }};
        const attemptId = {{ $attempt->id }};

        function goToQuestion(index) {
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

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function nextQuestion() {
            if (currentQuestionIndex < totalQuestions - 1) {
                goToQuestion(currentQuestionIndex + 1);
            }
        }

        function previousQuestion() {
            if (currentQuestionIndex > 0) {
                goToQuestion(currentQuestionIndex - 1);
            }
        }

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

        function updateProgress() {
            let answeredCount = 0;

            for (let i = 0; i < totalQuestions; i++) {
                const questionInputs = document.querySelectorAll(`input[name*="[${i}]"]`);
                let isAnswered = false;

                questionInputs.forEach(input => {
                    if (input.type === 'radio' && input.checked) {
                        isAnswered = true;
                    }
                });

                if (isAnswered) {
                    answeredCount++;
                    updateQuestionNavButton(i, true);
                } else {
                    updateQuestionNavButton(i, false);
                }
            }

            const progressPercent = (answeredCount / totalQuestions) * 100;

            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                progressBar.style.width = progressPercent + '%';
            }

            const progressText = document.getElementById('progress-text');
            if (progressText) {
                progressText.textContent = `${answeredCount} / ${totalQuestions}`;
            }

            const progressPercentage = document.getElementById('progress-percentage');
            if (progressPercentage) {
                progressPercentage.textContent = Math.round(progressPercent) + '%';
            }

            const modalAnsweredCount = document.getElementById('modal-answered-count');
            if (modalAnsweredCount) {
                modalAnsweredCount.textContent = `${answeredCount} / ${totalQuestions}`;
            }
        }

        function updateQuestionNavButton(index, isAnswered) {
            const navBtn = document.querySelector(`[data-question="${index}"]`);
            if (navBtn) {
                if (isAnswered) {
                    navBtn.classList.add('answered');
                } else {
                    navBtn.classList.remove('answered');
                }
            }
        }

        function updateQuestionStatus(questionIndex) {
            const questionSlide = document.querySelector(`[data-question-index="${questionIndex}"]`);
            if (!questionSlide) return;

            const statusIndicator = questionSlide.querySelector('.status-indicator');
            const statusText = questionSlide.querySelector('.status-text');
            const questionInputs = questionSlide.querySelectorAll('input[type="radio"]');
            let isAnswered = false;

            questionInputs.forEach(input => {
                if (input.checked) {
                    isAnswered = true;
                }
            });

            if (isAnswered) {
                statusIndicator.className = 'status-indicator w-4 h-4 rounded-full bg-green-500 transition-all duration-300';
                statusText.textContent = 'Sudah dijawab';
                statusText.className = 'status-text text-sm text-green-600 font-medium';
            } else {
                statusIndicator.className = 'status-indicator w-4 h-4 rounded-full border-2 border-gray-300 transition-all duration-300';
                statusText.textContent = 'Belum dijawab';
                statusText.className = 'status-text text-sm text-gray-500';
            }
        }

        function autoSave() {
            if (!quizId || !attemptId) {
                return;
            }

            const answers = getCurrentAnswers();
            localStorage.setItem(`quiz_backup_${quizId}`, JSON.stringify(answers));

            const saveIndicator = document.getElementById('save-indicator');
            if (saveIndicator) {
                saveIndicator.style.opacity = '1';
                setTimeout(() => {
                    saveIndicator.style.opacity = '0';
                }, 2000);
            }
        }

        function getCurrentAnswers() {
            const formData = new FormData(document.getElementById('quiz-attempt-form'));
            const answers = [];

            for (let i = 0; i < totalQuestions; i++) {
                const questionId = formData.get(`answers[${i}][question_id]`);
                const optionId = formData.get(`answers[${i}][option_id]`);
                const answerText = formData.get(`answers[${i}][answer_text]`);

                if (questionId && (optionId || answerText)) {
                    answers.push({
                        question_id: questionId,
                        option_id: optionId,
                        answer_text: answerText
                    });
                }
            }

            return answers;
        }

        function restoreAnswers() {
            const saved = localStorage.getItem(`quiz_backup_${quizId}`);
            if (!saved) return;

            try {
                const answers = JSON.parse(saved);

                answers.forEach(answer => {
                    let input = null;

                    if (answer.option_id) {
                        input = document.querySelector(`input[value="${answer.option_id}"]`);
                    } else if (answer.answer_text) {
                        input = document.querySelector(`input[value="${answer.answer_text}"]`);
                    }

                    if (input) {
                        input.checked = true;

                        const questionSlide = input.closest('.question-slide');
                        if (questionSlide) {
                            const questionIndex = parseInt(questionSlide.dataset.questionIndex);
                            updateQuestionStatus(questionIndex);
                        }
                    }
                });

                updateProgress();
            } catch (error) {
                console.error('Error restoring answers:', error);
            }
        }

        function autoSubmitQuiz() {
            autoSave();
            document.getElementById('quiz-attempt-form').submit();
        }

        function showSubmitConfirmation(event) {
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
        }

        function hideSubmitConfirmation() {
            const modal = document.getElementById('submit-modal');
            const modalContent = modal.querySelector('div > div');

            modalContent.style.transform = 'scale(0.8)';
            modalContent.style.opacity = '0';

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 200);
        }

        function submitQuiz() {
            localStorage.removeItem(`quiz_backup_${quizId}`);

            const submitBtn = event.target;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Kirim Jawaban';
            submitBtn.disabled = true;

            hideSubmitConfirmation();
            document.getElementById('quiz-attempt-form').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            restoreAnswers();
            updateProgress();
            updateNavigationButtons();

            for (let i = 0; i < totalQuestions; i++) {
                updateQuestionStatus(i);
            }

            setInterval(autoSave, 10000);
        });

        // Beforeunload warning removed for better browser compatibility
        // Answers are auto-saved every 10 seconds

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                nextQuestion();
            } else if (e.key === 'ArrowLeft') {
                previousQuestion();
            }
        });
    </script>
</x-app-layout>
