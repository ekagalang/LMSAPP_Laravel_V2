<x-app-layout>
    <x-slot name="header">
        <!-- Modern Quiz Header -->
        <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 text-white -mx-6 -mt-6 mb-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full bg-pattern"></div>
            </div>
            
            <!-- Floating Elements -->
            <div class="absolute top-4 right-4 w-20 h-20 bg-white/10 rounded-full blur-xl animate-pulse"></div>
            <div class="absolute bottom-4 left-4 w-16 h-16 bg-yellow-300/20 rounded-full blur-lg animate-bounce"></div>
            
            <div class="relative px-6 py-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Main Header Content -->
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between mb-8 space-y-6 lg:space-y-0">
                        <!-- Quiz Info Section -->
                        <div class="flex items-start space-x-6">
                            <!-- Quiz Icon -->
                            <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl border border-white/30 shadow-lg transform hover:scale-105 transition-all duration-300">
                                <i class="fas fa-brain text-3xl text-yellow-300 animate-pulse"></i>
                            </div>
                            
                            <!-- Quiz Details -->
                            <div class="space-y-2">
                                <div class="flex items-center space-x-3">
                                    <h1 class="text-4xl font-bold bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                                        {{ $quiz->title }}
                                    </h1>
                                    <span class="px-3 py-1 bg-green-500/20 text-green-200 text-sm font-medium rounded-full border border-green-400/30">
                                        Live
                                    </span>
                                </div>
                                
                                <div class="flex items-center space-x-4 text-blue-100">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-book-open text-lg"></i>
                                        <span class="font-medium">
                                            @if ($quiz->lesson && $quiz->lesson->course)
                                                {{ $quiz->lesson->course->title }}
                                            @else
                                                Kursus Tidak Ditemukan
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-clock text-lg"></i>
                                        <span>Started {{ $attempt->started_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                
                                <!-- Progress Indicator -->
                                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3 mt-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-blue-100">Quiz Progress</span>
                                        <span id="header-progress-text" class="text-sm text-white font-semibold">0%</span>
                                    </div>
                                    <div class="w-full bg-white/20 rounded-full h-2">
                                        <div id="header-progress-bar" class="bg-gradient-to-r from-yellow-400 to-orange-400 h-2 rounded-full transition-all duration-500 shadow-sm" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Timer Widget - Enhanced -->
                        @if ($quiz->time_limit)
                            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl transform hover:scale-105 transition-all duration-300">
                                <div class="text-center">
                                    <div class="flex items-center justify-center space-x-2 mb-3">
                                        <i class="fas fa-stopwatch text-yellow-300 text-lg"></i>
                                        <span class="text-sm font-medium text-blue-100">Sisa Waktu</span>
                                    </div>
                                    
                                    <!-- Circular Timer -->
                                    <div class="relative w-24 h-24 mx-auto mb-4">
                                        <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 100 100">
                                            <circle cx="50" cy="50" r="45" stroke="rgba(255,255,255,0.2)" stroke-width="8" fill="none"/>
                                            <circle id="timer-circle" cx="50" cy="50" r="45" stroke="url(#gradient)" stroke-width="8" fill="none" 
                                                    stroke-linecap="round" stroke-dasharray="283" stroke-dashoffset="0"
                                                    class="transition-all duration-1000"/>
                                            <defs>
                                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" style="stop-color:#fbbf24"/>
                                                    <stop offset="100%" style="stop-color:#f59e0b"/>
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                        
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div id="time-left" class="text-2xl font-bold font-mono text-white tracking-wider"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Timer Status -->
                                    <div id="timer-status" class="text-xs font-medium text-blue-200">
                                        <i class="fas fa-play text-green-400 mr-1"></i>
                                        In Progress
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Enhanced Quiz Stats Grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Total Questions -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-all duration-300 group">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-500/20 p-3 rounded-lg group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-list-ol text-blue-300 text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-white">{{ $quiz->questions->count() }}</div>
                                    <div class="text-sm text-blue-200 font-medium">Total Soal</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Marks -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-all duration-300 group">
                            <div class="flex items-center space-x-3">
                                <div class="bg-purple-500/20 p-3 rounded-lg group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-trophy text-purple-300 text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-white">{{ $quiz->total_marks }}</div>
                                    <div class="text-sm text-blue-200 font-medium">Total Nilai</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Passing Score -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-all duration-300 group">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-500/20 p-3 rounded-lg group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-check-circle text-green-300 text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-white">{{ $quiz->pass_marks }}</div>
                                    <div class="text-sm text-blue-200 font-medium">Nilai Lulus</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Time Limit -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-all duration-300 group">
                            <div class="flex items-center space-x-3">
                                <div class="bg-orange-500/20 p-3 rounded-lg group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-hourglass-half text-orange-300 text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-white">{{ $quiz->time_limit ?? '∞' }}</div>
                                    <div class="text-sm text-blue-200 font-medium">Menit</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6 pt-6 border-t border-white/20">
                        <div class="flex items-center space-x-4">
                            <!-- Quick Save Indicator -->
                            <div id="save-indicator" class="flex items-center space-x-2 text-green-300 opacity-0 transition-opacity duration-300">
                                <i class="fas fa-check-circle text-sm"></i>
                                <span class="text-sm font-medium">Auto-saved</span>
                            </div>
                        </div>
                        
                        <!-- Quiz Controls -->
                        <div class="flex items-center space-x-3">
                            <button onclick="showQuizHelp()" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 border border-white/20">
                                <i class="fas fa-question-circle mr-2"></i>
                                Bantuan
                            </button>
                            <button onclick="toggleFullscreen()" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 border border-white/20">
                                <i class="fas fa-expand mr-2"></i>
                                Fullscreen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Enhanced Progress Section -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-200">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 space-y-4 md:space-y-0">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Progress Pengerjaan</h3>
                        <p class="text-gray-600">Selesaikan semua soal dengan teliti</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span id="progress-text" class="text-lg font-semibold text-gray-700">0 dari {{ $quiz->questions->count() }} soal dijawab</span>
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                            <i class="fas fa-tasks mr-2"></i>
                            <span id="progress-percentage">0%</span>
                        </div>
                    </div>
                </div>
                
                <!-- Enhanced Progress Bar -->
                <div class="relative">
                    <div class="w-full bg-gray-200 rounded-full h-4 shadow-inner">
                        <div id="progress-bar" class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 h-4 rounded-full transition-all duration-700 shadow-lg relative overflow-hidden" style="width: 0%">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-3 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-play text-green-500 mr-1"></i>
                            Mulai
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-flag-checkered text-purple-500 mr-1"></i>
                            Selesai
                        </span>
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Navigasi Cepat:</span>
                        <div id="question-nav" class="flex flex-wrap gap-2">
                            @for($i = 1; $i <= $quiz->questions->count(); $i++)
                                <button onclick="scrollToQuestion({{ $i - 1 }})" 
                                        class="question-nav-btn w-8 h-8 rounded-lg border-2 border-gray-300 bg-white text-sm font-medium transition-all duration-200 hover:border-blue-400 hover:bg-blue-50"
                                        data-question="{{ $i - 1 }}">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Quiz Form -->
            <form id="quiz-attempt-form" method="POST" action="{{ route('quizzes.submit_attempt', [$quiz, $attempt]) }}">
                @csrf

                <div class="space-y-8">
                    @foreach ($quiz->questions as $index => $question)
                        <div class="question-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200" 
                             data-question-index="{{ $index }}"
                             id="question-{{ $index }}">
                            
                            <!-- Enhanced Question Header -->
                            <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 px-8 py-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg shadow-lg">
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
                                                <span class="text-sm text-blue-600 font-medium">
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

                            <!-- Enhanced Question Content -->
                            <div class="p-8 bg-white">
                                <div class="mb-8">
                                    <div class="prose prose-lg max-w-none">
                                        <p class="text-xl font-medium text-gray-900 leading-relaxed mb-6">
                                            {{ $question->question_text }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Enhanced Multiple Choice Options -->
                                @if ($question->type === 'multiple_choice')
                                    <div class="space-y-4">
                                        @foreach ($question->options as $optionIndex => $option)
                                            <label class="option-item group block relative cursor-pointer">
                                                <input type="radio" 
                                                       name="answers[{{ $index }}][option_id]" 
                                                       value="{{ $option->id }}" 
                                                       class="sr-only peer"
                                                       onchange="updateProgress(); autoSave(); updateQuestionStatus({{ $index }});">
                                                
                                                <div class="bg-white border-2 border-gray-200 rounded-xl p-6 transition-all duration-300 hover:border-blue-300 hover:shadow-lg hover:bg-blue-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg">
                                                    <div class="flex items-center space-x-4">
                                                        <!-- Custom Radio -->
                                                        <div class="relative">
                                                            <div class="w-6 h-6 rounded-full border-2 border-gray-300 bg-white transition-all duration-200 peer-checked:border-blue-500 group-hover:border-blue-400"></div>
                                                            <div class="absolute inset-0 flex items-center justify-center">
                                                                <div class="w-3 h-3 rounded-full bg-blue-500 scale-0 transition-transform duration-200 peer-checked:scale-100"></div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Option Letter -->
                                                        <div class="bg-gray-100 text-gray-700 w-10 h-10 rounded-lg flex items-center justify-center font-bold text-lg transition-all duration-200 peer-checked:bg-blue-100 peer-checked:text-blue-700">
                                                            {{ chr(65 + $optionIndex) }}
                                                        </div>
                                                        
                                                        <!-- Option Text -->
                                                        <div class="flex-1">
                                                            <span class="text-lg font-medium text-gray-800 transition-colors duration-200 peer-checked:text-blue-800">
                                                                {{ $option->option_text }}
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- Selection Indicator -->
                                                        <div class="opacity-0 transition-opacity duration-200 peer-checked:opacity-100">
                                                            <i class="fas fa-check-circle text-blue-500 text-xl"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">

                                <!-- Enhanced True/False Options -->
                                @elseif ($question->type === 'true_false')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- True Option -->
                                        <label class="option-item group block relative cursor-pointer">
                                            <input type="radio" 
                                                   name="answers[{{ $index }}][answer_text]" 
                                                   value="True" 
                                                   class="sr-only peer"
                                                   onchange="updateProgress(); autoSave(); updateQuestionStatus({{ $index }});">
                                            
                                            <div class="bg-white border-2 border-green-200 rounded-xl p-8 transition-all duration-300 hover:border-green-400 hover:shadow-lg hover:bg-green-50 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-lg relative">
                                                <!-- Selection Badge -->
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
                                            
                                            <div class="bg-white border-2 border-red-200 rounded-xl p-8 transition-all duration-300 hover:border-red-400 hover:shadow-lg hover:bg-red-50 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-lg relative">
                                                <!-- Selection Badge -->
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

                                <!-- Question Actions -->
                                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        @if($index > 0)
                                            <button type="button" onclick="scrollToQuestion({{ $index - 1 }})" 
                                                    class="bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                                <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center space-x-4">
                                        @if($index < $quiz->questions->count() - 1)
                                            <button type="button" onclick="scrollToQuestion({{ $index + 1 }})" 
                                                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                                Selanjutnya<i class="fas fa-chevron-right ml-2"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Enhanced Submit Section -->
                <div class="mt-12 bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="text-center">
                        <div class="mb-6">
                            <div class="bg-gradient-to-br from-green-100 to-emerald-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-flag-checkered text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900 mb-2">Selesai Mengerjakan?</h3>
                            <p class="text-lg text-gray-600 mb-4">Pastikan semua jawaban sudah terisi dengan benar</p>
                            
                            <!-- Submit Checklist -->
                            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Checklist Sebelum Submit:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                                    <div class="flex items-center space-x-3">
                                        <i id="check-all-answered" class="fas fa-square text-gray-400 text-lg"></i>
                                        <span class="text-gray-700">Semua soal telah dijawab</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-check-square text-green-500 text-lg"></i>
                                        <span class="text-gray-700">Jawaban tersimpan otomatis</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i id="check-time-remaining" class="fas fa-check-square text-green-500 text-lg"></i>
                                        <span class="text-gray-700">Waktu masih tersisa</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-check-square text-green-500 text-lg"></i>
                                        <span class="text-gray-700">Siap untuk dinilai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" 
                                onclick="showSubmitConfirmation()"
                                class="btn-submit bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-12 py-4 rounded-xl text-lg font-bold shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-3"></i>
                            Kirim Jawaban
                            <i class="fas fa-arrow-right ml-3"></i>
                        </button>
                        
                        <p class="text-sm text-gray-500 mt-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            Setelah dikirim, Anda tidak dapat mengubah jawaban lagi
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Enhanced Confirmation Modal -->
    <div id="submit-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 shadow-2xl">
            <div class="text-center">
                <!-- Modal Icon -->
                <div class="bg-gradient-to-br from-yellow-100 to-orange-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
                </div>
                
                <!-- Modal Content -->
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Konfirmasi Pengiriman</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Apakah Anda yakin ingin mengirim jawaban? 
                    <br><strong>Setelah dikirim, Anda tidak dapat mengubah jawaban lagi.</strong>
                </p>
                
                <!-- Summary -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Soal Dijawab:</span>
                            <br><span id="modal-answered-count" class="font-bold text-blue-600">0 / {{ $quiz->questions->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Sisa Waktu:</span>
                            <br><span id="modal-time-left" class="font-bold text-green-600">--:--</span>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Actions -->
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

    <!-- Simple Floating Action Buttons - 2 Buttons Only -->
    <div class="fixed bottom-6 right-6 z-40">
        <div class="flex space-x-4">
            <!-- Back to Top Button -->
            <button onclick="scrollToTop()" 
                    class="bg-blue-500 hover:bg-blue-600 text-white w-14 h-14 rounded-full shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center backdrop-blur-sm border-2 border-blue-400/30 group relative"
                    title="Scroll ke atas">
                <!-- Ensure icon is visible -->
                <span class="text-white text-xl font-bold group-hover:animate-bounce">↑</span>
            </button>
            
            <!-- Submit Button -->
            <button onclick="showSubmitConfirmation()" 
                    class="bg-green-500 hover:bg-green-600 text-white w-16 h-16 rounded-full shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center backdrop-blur-sm border-2 border-green-400/30 group relative"
                    title="Kirim jawaban">
                <!-- Ensure icon is visible -->
                <span class="text-white text-2xl font-bold group-hover:animate-pulse">✈</span>
                
                <!-- Pulse animation for submit button -->
                <div class="absolute inset-0 rounded-full bg-green-400 opacity-0 animate-ping group-hover:opacity-75"></div>
            </button>
        </div>
        
        <!-- Progress Badge -->
        <div class="mt-3 bg-white/95 backdrop-blur-sm rounded-lg px-4 py-2 shadow-lg border border-gray-200">
            <div class="text-sm text-gray-700 font-medium text-center">
                <span id="floating-progress">0%</span> selesai
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        /* Background Pattern */
        .bg-pattern {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 2px, transparent 2px);
            background-size: 50px 50px;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Enhanced Card Animations */
        .question-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
        }
        
        .question-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.15);
        }
        
        .question-card.answered {
            border-color: #10b981;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.15);
        }

        /* Enhanced Option Styling */
        .option-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .option-item:hover .peer:not(:checked) ~ div {
            border-color: #60a5fa !important;
            background-color: #f1f5f9 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15) !important;
        }
        
        .option-item .peer:checked ~ div {
            transform: translateY(-1px);
            border-width: 3px !important;
        }

        .option-item .peer:checked ~ div .bg-gray-100 {
            background: linear-gradient(135deg, #3b82f6, #1e40af) !important;
            color: white !important;
            transform: scale(1.1);
        }

        /* Enhanced Submit Button */
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

        /* True/False specific styles */
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

        .option-item .peer:checked ~ div .bg-green-100 {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            color: white !important;
            transform: scale(1.1);
        }

        .option-item .peer:checked ~ div .bg-red-100 {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: white !important;
            transform: scale(1.1);
        }

        /* Enhanced floating button icons */
        .fixed button span {
            font-size: inherit;
            line-height: 1;
            display: block;
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Ensure icons are always visible */
        .fixed button {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        }

        .fixed button:hover {
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
        }

        /* Enhanced Scroll Behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Submit button special effects */
        .fixed button.w-16.h-16 {
            position: relative;
            overflow: hidden;
        }

        .fixed button.w-16.h-16::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .fixed button.w-16.h-16:hover::before {
            left: 100%;
        }

        /* Floating button group entrance animation */
        .fixed.bottom-6.right-6 {
            animation: slideInFromRight 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideInFromRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive floating buttons - Updated for 2 buttons */
        @media (max-width: 768px) {
            .fixed.bottom-6.right-6 {
                bottom: 1rem;
                right: 1rem;
            }

            .fixed .flex.space-x-4 {
                gap: 0.75rem;
            }

            .fixed button.w-14.h-14 {
                width: 3rem;
                height: 3rem;
            }

            .fixed button.w-16.h-16 {
                width: 3.5rem;
                height: 3.5rem;
            }

            .fixed .text-lg {
                font-size: 1rem;
            }

            .fixed .text-xl {
                font-size: 1.125rem;
            }
        }

        /* Accessibility improvements */
        .option-item:focus-within {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .question-card,
            .option-item,
            .btn-submit {
                transition: none;
            }

            .question-card:hover,
            .option-item:hover {
                transform: none;
            }

            .bg-pattern {
                animation: none;
            }
        }
    </style>

    <!-- Enhanced JavaScript -->
    <script>
        // Enhanced timer functionality dengan circular progress
        @if ($quiz->time_limit && isset($timeRemaining) && $timeRemaining > 0)
            let timeLeft = Math.floor({{ $timeRemaining }});
            const totalTime = {{ $quiz->time_limit }} * 60;
            const circumference = 2 * Math.PI * 45; // radius = 45
            
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
                
                // Update modal time
                const modalTimeEl = document.getElementById('modal-time-left');
                if (modalTimeEl) {
                    modalTimeEl.textContent = formattedTime;
                }
                
                // Update circular progress
                const progress = (totalTime - totalSeconds) / totalTime;
                const offset = circumference * progress;
                const circle = document.getElementById('timer-circle');
                if (circle) {
                    circle.style.strokeDashoffset = offset;
                }
                
                // Update timer status and warnings
                updateTimerStatus(totalSeconds);
                
                timeLeft = Math.max(0, timeLeft - 1);
            }
            
            function updateTimerStatus(seconds) {
                const statusEl = document.getElementById('timer-status');
                const timerEl = document.getElementById('time-left');
                const checkTimeEl = document.getElementById('check-time-remaining');
                
                if (seconds <= 300) { // 5 minutes warning
                    statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-400 mr-1 animate-pulse"></i>Hampir Habis!';
                    timerEl.style.color = '#fbbf24';
                    if (checkTimeEl) checkTimeEl.className = 'fas fa-exclamation-triangle text-yellow-500 text-lg';
                } else if (seconds <= 600) { // 10 minutes warning
                    statusEl.innerHTML = '<i class="fas fa-clock text-orange-400 mr-1"></i>Perhatikan Waktu';
                    if (checkTimeEl) checkTimeEl.className = 'fas fa-clock text-orange-500 text-lg';
                } else {
                    statusEl.innerHTML = '<i class="fas fa-play text-green-400 mr-1"></i>In Progress';
                    if (checkTimeEl) checkTimeEl.className = 'fas fa-check-square text-green-500 text-lg';
                }
            }
            
            function showTimeUpAlert() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Waktu Habis!',
                    text: 'Jawaban yang sudah terisi akan dinilai otomatis.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3b82f6',
                    allowOutsideClick: false
                }).then(() => {
                    autoSubmitQuiz();
                });
            }
            
            const timerInterval = setInterval(updateTimerDisplay, 1000);
            updateTimerDisplay();
        @endif

        // Global variables
        const quizId = {{ $quiz->id }};
        const attemptId = {{ $attempt->id }};
        const totalQuestions = {{ $quiz->questions->count() }};

        // Enhanced progress tracking
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
            
            // Update main progress bar
            const progressBar = document.getElementById('progress-bar');
            if (progressBar) {
                progressBar.style.width = progressPercent + '%';
            }
            
            const progressText = document.getElementById('progress-text');
            if (progressText) {
                progressText.textContent = `${answeredCount} dari ${totalQuestions} soal dijawab`;
            }

            const progressPercentage = document.getElementById('progress-percentage');
            if (progressPercentage) {
                progressPercentage.textContent = Math.round(progressPercent) + '%';
            }
            
            // Update header progress
            const headerProgressBar = document.getElementById('header-progress-bar');
            if (headerProgressBar) {
                headerProgressBar.style.width = progressPercent + '%';
            }
            
            const headerProgressText = document.getElementById('header-progress-text');
            if (headerProgressText) {
                headerProgressText.textContent = Math.round(progressPercent) + '%';
            }

            // Update floating progress badge
            const floatingProgress = document.getElementById('floating-progress');
            if (floatingProgress) {
                floatingProgress.textContent = Math.round(progressPercent) + '%';
            }

            // Update modal answered count
            const modalAnsweredCount = document.getElementById('modal-answered-count');
            if (modalAnsweredCount) {
                modalAnsweredCount.textContent = `${answeredCount} / ${totalQuestions}`;
            }

            // Update checklist
            updateSubmitChecklist(answeredCount);
        }

        function updateQuestionNavButton(index, isAnswered) {
            const navBtn = document.querySelector(`[data-question="${index}"]`);
            if (navBtn) {
                if (isAnswered) {
                    navBtn.className = 'question-nav-btn w-8 h-8 rounded-lg border-2 border-green-400 bg-green-100 text-sm font-medium transition-all duration-200 text-green-700';
                } else {
                    navBtn.className = 'question-nav-btn w-8 h-8 rounded-lg border-2 border-gray-300 bg-white text-sm font-medium transition-all duration-200 hover:border-blue-400 hover:bg-blue-50';
                }
            }
        }

        function updateQuestionStatus(questionIndex) {
            const questionCard = document.querySelector(`[data-question-index="${questionIndex}"]`);
            if (!questionCard) return;

            const statusIndicator = questionCard.querySelector('.status-indicator');
            const statusText = questionCard.querySelector('.status-text');

            const questionInputs = questionCard.querySelectorAll('input[type="radio"]');
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
                questionCard.classList.add('answered');
            } else {
                statusIndicator.className = 'status-indicator w-4 h-4 rounded-full border-2 border-gray-300 transition-all duration-300';
                statusText.textContent = 'Belum dijawab';
                statusText.className = 'status-text text-sm text-gray-500';
                questionCard.classList.remove('answered');
            }
        }

        function updateSubmitChecklist(answeredCount) {
            const checkAllAnswered = document.getElementById('check-all-answered');
            if (checkAllAnswered) {
                if (answeredCount === totalQuestions) {
                    checkAllAnswered.className = 'fas fa-check-square text-green-500 text-lg';
                } else {
                    checkAllAnswered.className = 'fas fa-square text-gray-400 text-lg';
                }
            }
        }

        function autoSave() {
            if (!quizId || !attemptId) {
                return;
            }
            
            const answers = getCurrentAnswers();
            localStorage.setItem(`quiz_backup_${quizId}`, JSON.stringify(answers));
            
            // Show save indicator
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
                        
                        const questionCard = input.closest('.question-card');
                        if (questionCard) {
                            const questionIndex = parseInt(questionCard.dataset.questionIndex);
                            updateQuestionStatus(questionIndex);
                        }
                    }
                });
                
                updateProgress();
            } catch (error) {
                console.error('Error restoring answers:', error);
            }
        }

        // Enhanced navigation functions with smooth scrolling
        function scrollToQuestion(index) {
            const questionElement = document.getElementById(`question-${index}`);
            if (questionElement) {
                // Enhanced smooth scroll with offset for header
                const headerHeight = 120; // Adjust based on your header height
                const elementTop = questionElement.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementTop - headerHeight;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Highlight animation
                questionElement.style.transform = 'scale(1.02)';
                questionElement.style.boxShadow = '0 0 20px rgba(59, 130, 246, 0.3)';
                
                setTimeout(() => {
                    questionElement.style.transform = 'scale(1)';
                    questionElement.style.boxShadow = '';
                }, 500);
            }
        }

        function scrollToTop() {
            window.scrollTo({ 
                top: 0, 
                behavior: 'smooth' 
            });
            
            // Add visual feedback
            const btn = event.target.closest('button');
            btn.style.transform = 'scale(0.9)';
            setTimeout(() => {
                btn.style.transform = 'scale(1)';
            }, 150);
        }

        function scrollToProgress() {
            const progressElement = document.querySelector('.bg-white.rounded-2xl.shadow-lg');
            if (progressElement) {
                const headerHeight = 120;
                const elementTop = progressElement.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementTop - headerHeight;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // Highlight the progress section
                progressElement.style.transform = 'scale(1.01)';
                progressElement.style.boxShadow = '0 0 25px rgba(99, 102, 241, 0.2)';
                
                setTimeout(() => {
                    progressElement.style.transform = 'scale(1)';
                    progressElement.style.boxShadow = '';
                }, 800);
            }
        }

        function autoSubmitQuiz() {
            autoSave();
            document.getElementById('quiz-attempt-form').submit();
        }

        function showSubmitConfirmation() {
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
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
            submitBtn.disabled = true;
            
            document.getElementById('quiz-attempt-form').submit();
        }

        function showQuizHelp() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Bantuan Quiz',
                    html: `
                        <div class="text-left space-y-4">
                            <div>
                                <h4 class="font-bold text-lg mb-2">💡 Tips Mengerjakan:</h4>
                                <ul class="list-disc list-inside space-y-2 text-sm">
                                    <li>Jawaban otomatis tersimpan setiap 10 detik</li>
                                    <li>Anda bisa refresh halaman tanpa kehilangan progress</li>
                                    <li>Gunakan navigasi cepat untuk melompat ke soal tertentu</li>
                                    <li>Perhatikan timer di pojok kanan atas</li>
                                    <li>Pastikan semua soal terjawab sebelum submit</li>
                                </ul>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3b82f6',
                    width: '500px'
                });
            } else {
                alert('Tips: Jawaban tersimpan otomatis. Gunakan navigasi cepat untuk berpindah soal. Pastikan semua soal terjawab sebelum submit.');
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen error:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                showSubmitConfirmation();
            }
            
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                autoSave();
            }
        });

        // Enhanced scroll spy to track current question
        function initScrollSpy() {
            let ticking = false;
            
            function updateActiveQuestion() {
                const questions = document.querySelectorAll('.question-card');
                const headerHeight = 150;
                let activeQuestionIndex = -1;
                
                questions.forEach((question, index) => {
                    const rect = question.getBoundingClientRect();
                    if (rect.top <= headerHeight && rect.bottom > headerHeight) {
                        activeQuestionIndex = index;
                    }
                });
                
                // Update navigation buttons highlight
                document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
                    if (index === activeQuestionIndex) {
                        btn.classList.add('ring-2', 'ring-blue-400', 'ring-offset-2');
                    } else {
                        btn.classList.remove('ring-2', 'ring-blue-400', 'ring-offset-2');
                    }
                });
                
                ticking = false;
            }
            
            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateActiveQuestion);
                    ticking = true;
                }
            }
            
            window.addEventListener('scroll', requestTick);
        }

        // Initialize enhanced features
        document.addEventListener('DOMContentLoaded', function() {
            restoreAnswers();
            updateProgress();
            initScrollSpy(); // Add scroll spy
            
            for (let i = 0; i < totalQuestions; i++) {
                updateQuestionStatus(i);
            }
            
            setInterval(autoSave, 10000);
        });

        // Prevent accidental page close
        window.addEventListener('beforeunload', function(e) {
            if (typeof timeLeft !== 'undefined' && timeLeft > 0) {
                e.preventDefault();
                e.returnValue = 'Anda yakin ingin meninggalkan halaman? Jawaban akan tersimpan otomatis.';
            }
        });
    </script>

    <!-- Include SweetAlert2 for better alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-app-layout>