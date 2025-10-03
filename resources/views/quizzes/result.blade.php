<x-app-layout>
    <x-slot name="header">
        <div class="gradient-bg text-white py-8 -mx-6 -mt-6 mb-6">
            <div class="px-6">
                <div class="flex items-center space-x-3 mb-2">
                    <i class="fas fa-clipboard-check text-2xl"></i>
                    <h2 class="text-3xl font-bold">Hasil Kuis</h2>
                </div>
                <p class="text-blue-100 text-lg">{{ $quiz->title }}</p>
            </div>
        </div>
    </x-slot>

    <!-- Add required CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .success-gradient {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }
        
        .fail-gradient {
            background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
        }
        
        .score-circle {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .score-circle::before {
            content: '';
            position: absolute;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: white;
        }
        
        .score-text {
            position: relative;
            z-index: 1;
        }
        
        .bounce-in {
            animation: bounceIn 0.8s ease-out;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .pulse-icon {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Button hover effects */
        .btn-hover {
            transition: all 0.3s ease;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
    </style>

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Result Card -->
            <div class="bg-white rounded-xl card-shadow overflow-hidden bounce-in">
                <!-- Status Banner -->
                @php
                    $percentage = $attempt->percentage ?? 0;
                    $isPassed = $attempt->passed;
                @endphp
                
                <div class="{{ $isPassed ? 'success-gradient' : 'fail-gradient' }} text-white p-6 text-center">
                    <div class="flex items-center justify-center space-x-3 mb-3">
                        <i class="fas {{ $isPassed ? 'fa-check-circle' : 'fa-times-circle' }} text-4xl pulse-icon"></i>
                        <h2 class="text-2xl font-bold">{{ $isPassed ? 'Selamat! Anda Lulus' : 'Tidak Lulus' }}</h2>
                    </div>
                    <p class="{{ $isPassed ? 'text-green-100' : 'text-red-100' }}">
                        {{ $isPassed ? 'Excellent work! Keep up the great progress!' : 'Jangan menyerah! Terus belajar dan coba lagi' }}
                    </p>
                </div>

                <div class="p-8">
                    <!-- Score Section -->
                    <div class="flex flex-col lg:flex-row items-center justify-between mb-8">
                        <div class="flex items-center space-x-6 mb-6 lg:mb-0">
                            <div class="score-circle" style="background: conic-gradient({{ $isPassed ? '#4ade80' : '#ef4444' }} 0deg {{ $percentage * 3.6 }}deg, #e5e7eb {{ $percentage * 3.6 }}deg 360deg);">
                                <div class="score-text text-center">
                                    <div class="text-3xl font-bold text-gray-800">{{ number_format($percentage, 1) }}%</div>
                                    <div class="text-sm text-gray-500">Nilai Anda</div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Persentase Nilai</h3>
                                <div class="flex items-center space-x-4">
                                    <span class="text-3xl font-bold {{ $isPassed ? 'text-green-500' : 'text-red-500' }}">{{ number_format($percentage, 1) }}%</span>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    Minimum untuk lulus: {{ $quiz->passing_percentage }}%
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <i class="fas fa-question-circle text-blue-500 text-xl mb-2"></i>
                                <div class="text-sm text-gray-600">Total Soal</div>
                                <div class="text-lg font-semibold text-blue-600">{{ $quiz->questions->count() }}</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <i class="fas fa-check-circle text-green-500 text-xl mb-2"></i>
                                <div class="text-sm text-gray-600">Benar</div>
                                @php
                                    $correctAnswers = 0;
                                    foreach($quiz->questions as $question) {
                                        $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                                        if($userAnswer && $userAnswer->option && $userAnswer->option->is_correct) {
                                            $correctAnswers++;
                                        }
                                    }
                                @endphp
                                <div class="text-lg font-semibold text-green-600">{{ $correctAnswers }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progress Kuis</span>
                            <span class="text-sm text-gray-500">{{ $percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $isPassed ? 'bg-green-400' : 'bg-red-400' }}" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Quiz Info -->
                    <div class="grid md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-clock text-gray-500"></i>
                                <div>
                                    <div class="text-sm text-gray-600">Waktu Selesai</div>
                                    <div class="font-semibold">{{ $attempt->formatted_completed_at ?? 'Just now' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-hourglass-half text-gray-500"></i>
                                <div>
                                    <div class="text-sm text-gray-600">Durasi</div>
                                    <div class="font-semibold">{{ $attempt->duration ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-target text-gray-500"></i>
                                <div>
                                    <div class="text-sm text-gray-600">Passing Grade</div>
                                    <div class="font-semibold">{{ $quiz->passing_percentage }}%</div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-trophy text-gray-500"></i>
                                <div>
                                    <div class="text-sm text-gray-600">Status</div>
                                    <div class="font-semibold {{ $isPassed ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $isPassed ? 'Lulus' : 'Perlu Perbaikan' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz Answers Detail -->
                    @if($quiz->show_answers_after_attempt)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8">
                            <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-list-check mr-2 text-blue-500"></i>
                                Detail Jawaban
                            </h4>
                            
                            <div class="space-y-6">
                                @foreach($quiz->questions as $question)
                                    <div class="border-b pb-4 last:border-b-0">
                                        <p class="font-semibold mb-2">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                                        <p class="text-sm text-gray-600 mb-3">({{ $question->marks }} Poin)</p>

                                        @php
                                            $userAnswer = $attempt->answers->where('question_id', $question->id)->first();
                                            $correctOption = $question->options->where('is_correct', true)->first();
                                        @endphp

                                        <div class="space-y-2">
                                            @foreach($question->options as $option)
                                                @php
                                                    $isUserAnswer = $userAnswer && $userAnswer->option_id == $option->id;
                                                    $isCorrect = $option->is_correct;
                                                    $class = '';
                                                    $iconClass = '';
                                                    $label = '';

                                                    if ($isUserAnswer && $isCorrect) {
                                                        $class = 'bg-green-100 border-green-400 text-green-800';
                                                        $iconClass = 'fas fa-check-circle text-green-500';
                                                        $label = 'Jawaban Anda Benar';
                                                    } elseif ($isUserAnswer && !$isCorrect) {
                                                        $class = 'bg-red-100 border-red-400 text-red-800';
                                                        $iconClass = 'fas fa-times-circle text-red-500';
                                                        $label = 'Jawaban Anda Salah';
                                                    } elseif (!$isUserAnswer && $isCorrect) {
                                                        $class = 'bg-blue-100 border-blue-400 text-blue-800';
                                                        $iconClass = 'fas fa-lightbulb text-blue-500';
                                                        $label = 'Kunci Jawaban';
                                                    } else {
                                                        $class = 'bg-gray-100 border-gray-300 text-gray-700';
                                                        $iconClass = 'fas fa-circle text-gray-400';
                                                    }
                                                @endphp
                                                <div class="p-3 rounded-lg border {{ $class }} flex items-center justify-between">
                                                    <span>{{ $option->option_text }}</span>
                                                    @if($label)
                                                        <div class="flex items-center space-x-2">
                                                            <i class="{{ $iconClass }}"></i>
                                                            <span class="text-sm font-medium">{{ $label }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        @if(!$userAnswer)
                                            <div class="mt-2 p-3 rounded-lg border bg-yellow-100 border-yellow-400 flex items-center space-x-2">
                                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                                <p class="text-yellow-800 font-medium">Tidak Dijawab</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-yellow-500 text-xl mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-yellow-800 mb-2">Tinjauan Jawaban</h4>
                                    <p class="text-yellow-700">Tinjauan jawaban tidak tersedia untuk kuis ini. Hubungi instruktur jika Anda memerlukan feedback lebih detail.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recommendations -->
                    @if(!$isPassed)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                            <h4 class="font-semibold text-blue-800 mb-4 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                Rekomendasi untuk Anda
                            </h4>
                            <ul class="space-y-2 text-blue-700">
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-arrow-right text-blue-500 mt-1 text-sm"></i>
                                    <span>Ulangi materi pelajaran sebelum mencoba kuis lagi</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-arrow-right text-blue-500 mt-1 text-sm"></i>
                                    <span>Diskusikan dengan instruktur atau teman untuk pemahaman yang lebih baik</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-arrow-right text-blue-500 mt-1 text-sm"></i>
                                    <span>Praktikkan latihan soal tambahan jika tersedia</span>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                            <h4 class="font-semibold text-green-800 mb-4 flex items-center">
                                <i class="fas fa-trophy mr-2"></i>
                                Selamat!
                            </h4>
                            <p class="text-green-700">Anda telah berhasil menyelesaikan kuis ini dengan baik. Lanjutkan ke materi berikutnya untuk melanjutkan pembelajaran Anda.</p>
                        </div>
                    @endif

                    <!-- Leaderboard Preview -->
                    @if($quiz->enable_leaderboard)
                        @php
                            $leaderboard = $quiz->getLeaderboardWithBestAttempts(5);
                            $userRank = $leaderboard->where('user.id', Auth::id())->first();
                        @endphp

                        @if($leaderboard->count() > 0)
                            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-200 rounded-xl p-6 mb-8">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-bold text-gray-800 flex items-center text-lg">
                                        <svg class="w-6 h-6 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Top 5 Leaderboard
                                    </h4>
                                    <a href="{{ route('quizzes.leaderboard', $quiz) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Lihat Semua →
                                    </a>
                                </div>

                                <div class="space-y-2">
                                    @foreach($leaderboard as $entry)
                                        @php
                                            $isCurrentUser = $entry['user']->id === Auth::id();
                                            $medalColors = ['text-yellow-500', 'text-gray-400', 'text-amber-600'];
                                            $medalColor = $medalColors[$entry['rank'] - 1] ?? 'text-gray-400';
                                        @endphp
                                        <div class="flex items-center justify-between p-3 rounded-lg {{ $isCurrentUser ? 'bg-blue-100 border-2 border-blue-400' : 'bg-white' }}">
                                            <div class="flex items-center space-x-3">
                                                <span class="font-bold text-lg {{ $medalColor }} w-8">
                                                    @if($entry['rank'] <= 3)
                                                        <i class="fas fa-medal"></i>
                                                    @else
                                                        #{{ $entry['rank'] }}
                                                    @endif
                                                </span>
                                                <div>
                                                    <div class="font-semibold {{ $isCurrentUser ? 'text-blue-900' : 'text-gray-900' }}">
                                                        {{ $entry['user']->name }} {{ $isCurrentUser ? '(Anda)' : '' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $entry['completed_at']->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-lg {{ $entry['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($entry['percentage'], 1) }}%
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ number_format($entry['percentage'], 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($userRank)
                                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="text-sm text-blue-800">
                                            <strong>Peringkat Anda:</strong> #{{ $userRank['rank'] }} dari {{ $leaderboard->count() }} peserta
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        {{-- [FIX] Kembali ke halaman content --}}
                        <a href="{{ route('contents.show', $content) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold btn-hover flex items-center justify-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali ke Materi</span>
                        </a>

                        <!-- Tombol Coba Lagi - HANYA JIKA TIDAK LULUS -->
                        @if(!$hasPassedQuizBefore)
                            <a href="{{ route('quizzes.start', $quiz) }}"
                               class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold btn-hover flex items-center justify-center space-x-2">
                                <i class="fas fa-redo"></i>
                                <span>Coba Lagi</span>
                            </a>
                        @endif
                        
                        <!-- Tombol Download/Print -->
                        <button onclick="window.print()" 
                                class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-lg font-semibold btn-hover flex items-center justify-center space-x-2">
                            <i class="fas fa-download"></i>
                            <span>Unduh Hasil</span>
                        </button>
                        
                        <!-- Tombol Lanjut ke Materi Berikutnya (jika lulus) -->
                        @if($isPassed)
                             {{-- [PERBAIKAN] Arahkan juga ke halaman materi agar pengguna bisa klik tombol 'selesai' --}}
                             <a href="{{ route('contents.show', $content->id) }}" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2">
                                <i class="fas fa-check"></i>
                                <span>Lanjut Belajar</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- [DIKEMBALIKAN] Additional Info Cards -->
            <div class="grid md:grid-cols-2 gap-6 mt-8 mb-12">
                <!-- Performance Chart -->
                <div class="bg-white rounded-lg card-shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                        Analisis Performa
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Jawaban Benar</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $quiz->questions->count() > 0 ? ($correctAnswers / $quiz->questions->count()) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $quiz->questions->count() > 0 ? round(($correctAnswers / $quiz->questions->count()) * 100) : 0 }}%</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Jawaban Salah</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $quiz->questions->count() > 0 ? (($quiz->questions->count() - $correctAnswers) / $quiz->questions->count()) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $quiz->questions->count() > 0 ? round((($quiz->questions->count() - $correctAnswers) / $quiz->questions->count()) * 100) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-white rounded-lg card-shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-route mr-2 text-purple-500"></i>
                        Langkah Selanjutnya
                    </h3>
                    <div class="space-y-3">
                        @if($isPassed)
                            <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 font-semibold text-sm">1</span>
                                </div>
                                <span class="text-sm text-gray-700">Lanjutkan ke materi berikutnya</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-sm">1</span>
                                </div>
                                <span class="text-sm text-gray-700">Review materi pelajaran</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-green-600 font-semibold text-sm">2</span>
                                </div>
                                <span class="text-sm text-gray-700">Diskusi dengan instruktur</span>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-purple-50 rounded-lg">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-purple-600 font-semibold text-sm">3</span>
                                </div>
                                <span class="text-sm text-gray-700">Ulangi kuis</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</x-app-layout>
