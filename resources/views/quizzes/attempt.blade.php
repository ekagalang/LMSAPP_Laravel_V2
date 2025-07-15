<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white -mx-6 -mt-6 mb-6 px-6 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-clipboard-check text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold">{{ $quiz->title }}</h2>
                            <p class="text-blue-100 mt-1">
                                <i class="fas fa-book mr-2"></i>
                                @if ($quiz->lesson && $quiz->lesson->course)
                                    {{ $quiz->lesson->course->title }}
                                @else
                                    Kursus Tidak Ditemukan
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Timer Widget -->
                    @if ($quiz->time_limit)
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                            <div class="text-sm text-blue-100 mb-1">Sisa Waktu</div>
                            <div id="time-left" class="text-2xl font-bold font-mono"></div>
                            <div class="w-16 h-1 bg-white/30 rounded-full mt-2">
                                <div id="time-progress" class="h-full bg-white rounded-full transition-all duration-1000"></div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Quiz Info Bar -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 text-center">
                        <div class="text-lg font-bold">{{ $quiz->questions->count() }}</div>
                        <div class="text-sm text-blue-100">Total Soal</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 text-center">
                        <div class="text-lg font-bold">{{ $quiz->total_marks }}</div>
                        <div class="text-sm text-blue-100">Total Nilai</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 text-center">
                        <div class="text-lg font-bold">{{ $quiz->pass_marks }}</div>
                        <div class="text-sm text-blue-100">Nilai Lulus</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 text-center">
                        <div class="text-lg font-bold">{{ $quiz->time_limit ?? '∞' }}</div>
                        <div class="text-sm text-blue-100">Menit</div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Custom Styles -->
    <style>
        .question-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .question-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .question-card.answered {
            border-color: #10b981;
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
        }
        
        .option-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .option-item:hover {
            background-color: #f3f4f6;
            transform: translateX(4px);
        }
        
        .option-item.selected {
            background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);
            border-color: #3b82f6;
            transform: translateX(4px);
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .progress-ring circle {
            stroke-linecap: round;
            transition: stroke-dashoffset 0.3s ease;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .timer-warning {
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Bar -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Progress Pengerjaan</h3>
                    <span id="progress-text" class="text-sm text-gray-600">0 dari {{ $quiz->questions->count() }} soal dijawab</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div id="progress-bar" class="bg-gradient-to-r from-blue-500 to-indigo-500 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>Mulai</span>
                    <span>Selesai</span>
                </div>
            </div>

            <!-- Quiz Form -->
            <form id="quiz-attempt-form" method="POST" action="{{ route('quizzes.submit_attempt', [$quiz, $attempt]) }}">
                @csrf

                <div class="space-y-6">
                    @foreach ($quiz->questions as $index => $question)
                        <div class="question-card bg-white rounded-xl shadow-lg overflow-hidden fade-in" 
                             data-question-index="{{ $index }}"
                             style="animation-delay: {{ $index * 0.1 }}s">
                            
                            <!-- Question Header -->
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">
                                            Soal {{ $index + 1 }} dari {{ $quiz->questions->count() }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <span class="text-sm font-semibold text-gray-700">{{ $question->marks }} poin</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Question Content -->
                            <div class="p-6">
                                <div class="mb-6">
                                    <p class="text-lg font-medium text-gray-900 leading-relaxed">
                                        {{ $question->question_text }}
                                    </p>
                                </div>

                                <!-- Multiple Choice Options -->
                                @if ($question->type === 'multiple_choice')
                                    <div class="space-y-3">
                                        @foreach ($question->options as $optionIndex => $option)
                                            <label class="option-item block p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300">
                                                <div class="flex items-center space-x-3">
                                                    <input type="radio" 
                                                           name="answers[{{ $index }}][option_id]" 
                                                           value="{{ $option->id }}" 
                                                           class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                                                           onchange="updateProgress()"
                                                           required>
                                                    <div class="bg-gray-100 text-gray-600 w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm">
                                                        {{ chr(65 + $optionIndex) }}
                                                    </div>
                                                    <span class="text-gray-700 font-medium flex-1">{{ $option->option_text }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">

                                <!-- True/False Options -->
                                @elseif ($question->type === 'true_false')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label class="option-item block p-4 border-2 border-gray-200 rounded-lg hover:border-green-300">
                                            <div class="flex items-center space-x-3">
                                                <input type="radio" 
                                                       name="answers[{{ $index }}][answer_text]" 
                                                       value="True" 
                                                       class="w-5 h-5 text-green-600 border-gray-300 focus:ring-green-500"
                                                       onchange="updateProgress()"
                                                       required>
                                                <div class="bg-green-100 text-green-600 w-8 h-8 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <span class="text-gray-700 font-medium">Benar (True)</span>
                                            </div>
                                        </label>
                                        
                                        <label class="option-item block p-4 border-2 border-gray-200 rounded-lg hover:border-red-300">
                                            <div class="flex items-center space-x-3">
                                                <input type="radio" 
                                                       name="answers[{{ $index }}][answer_text]" 
                                                       value="False" 
                                                       class="w-5 h-5 text-red-600 border-gray-300 focus:ring-red-500"
                                                       onchange="updateProgress()"
                                                       required>
                                                <div class="bg-red-100 text-red-600 w-8 h-8 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-times"></i>
                                                </div>
                                                <span class="text-gray-700 font-medium">Salah (False)</span>
                                            </div>
                                        </label>
                                    </div>
                                    <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Submit Section -->
                <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Selesai Mengerjakan?</h3>
                            <p class="text-sm text-gray-600 mt-1">Pastikan semua jawaban sudah terisi sebelum mengirim</p>
                        </div>
                        <button type="button" 
                                onclick="showSubmitConfirmation()"
                                class="btn-submit px-8 py-3 text-white font-semibold rounded-lg shadow-lg flex items-center space-x-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Kirim Jawaban</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="submit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-md mx-4 transform transition-all">
            <div class="text-center">
                <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Pengiriman</h3>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin mengirim jawaban? Setelah dikirim, Anda tidak dapat mengubah jawaban lagi.</p>
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="hideSubmitConfirmation()"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batalkan
                    </button>
                    <button type="button" 
                            onclick="submitQuiz()"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Ya, Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Timer functionality
        @if ($quiz->time_limit)
            const timeLimit = {{ $quiz->time_limit }}; // in minutes
            let timeLeft = timeLimit * 60; // in seconds
            const totalTime = timeLeft;
            const timeLeftDisplay = document.getElementById('time-left');
            const timeProgress = document.getElementById('time-progress');
            const quizForm = document.getElementById('quiz-attempt-form');

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timeLeftDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                // Update progress bar
                const progressPercent = ((totalTime - timeLeft) / totalTime) * 100;
                timeProgress.style.width = progressPercent + '%';
                
                // Warning when less than 5 minutes
                if (timeLeft <= 300) {
                    timeLeftDisplay.parentElement.classList.add('timer-warning');
                    timeLeftDisplay.style.color = '#ef4444';
                }
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    alert('Waktu kuis habis! Jawaban Anda akan dikirim otomatis.');
                    quizForm.submit();
                } else {
                    timeLeft--;
                }
            }

            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer();
        @endif

        // Progress tracking
        function updateProgress() {
            const totalQuestions = {{ $quiz->questions->count() }};
            let answeredCount = 0;
            
            // Count answered questions
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
                    // Mark question card as answered
                    const questionCard = document.querySelector(`[data-question-index="${i}"]`);
                    questionCard.classList.add('answered');
                } else {
                    const questionCard = document.querySelector(`[data-question-index="${i}"]`);
                    questionCard.classList.remove('answered');
                }
            }
            
            // Update progress bar
            const progressPercent = (answeredCount / totalQuestions) * 100;
            document.getElementById('progress-bar').style.width = progressPercent + '%';
            document.getElementById('progress-text').textContent = `${answeredCount} dari ${totalQuestions} soal dijawab`;
        }

        // Option selection styling
        document.addEventListener('DOMContentLoaded', function() {
            const options = document.querySelectorAll('.option-item');
            
            options.forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                
                radio.addEventListener('change', function() {
                    // Remove selected class from all options in this question
                    const questionOptions = option.closest('.question-card').querySelectorAll('.option-item');
                    questionOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to chosen option
                    if (this.checked) {
                        option.classList.add('selected');
                    }
                });
            });
        });

        // Modal functions
        function showSubmitConfirmation() {
            document.getElementById('submit-modal').classList.remove('hidden');
            document.getElementById('submit-modal').classList.add('flex');
        }

        function hideSubmitConfirmation() {
            document.getElementById('submit-modal').classList.add('hidden');
            document.getElementById('submit-modal').classList.remove('flex');
        }

        function submitQuiz() {
            document.getElementById('quiz-attempt-form').submit();
        }

        // Auto-save functionality (optional)
        function autoSave() {
            // This could save answers to localStorage as backup
            const formData = new FormData(document.getElementById('quiz-attempt-form'));
            const answers = {};
            
            for (let [key, value] of formData.entries()) {
                answers[key] = value;
            }
            
            localStorage.setItem('quiz_backup_{{ $quiz->id }}', JSON.stringify(answers));
        }

        // Auto-save every 30 seconds
        setInterval(autoSave, 30000);

        // Prevent accidental page close
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = 'Anda yakin ingin meninggalkan halaman? Jawaban yang belum tersimpan akan hilang.';
        });
    </script>

    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</x-app-layout>