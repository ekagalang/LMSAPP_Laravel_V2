<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Grade Essay: {{ $submission->content->title }}
            </h2>
            <div class="flex items-center space-x-3">
                {{-- Status badge untuk scoring --}}
                @if($submission->content->scoring_enabled ?? true)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Dengan Penilaian
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Tanpa Penilaian
                    </span>
                @endif
                <a href="{{ route('courses.gradebook', $submission->content->lesson->course) }}" 
                   class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Back to Gradebook
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            
            @php
                $totalQuestions = $submission->content->essayQuestions()->count();
                $scoringEnabled = $submission->content->scoring_enabled ?? true;
                $gradingMode = $submission->content->grading_mode ?? 'individual';
                
                // PERBAIKAN: Logic graded answers yang benar untuk semua mode
                if ($gradingMode === 'overall') {
                    if ($scoringEnabled) {
                        // Overall + Scoring: Cek answer pertama punya score
                        $firstAnswer = $submission->answers()->first();
                        $gradedAnswers = ($firstAnswer && $firstAnswer->score !== null) ? $totalQuestions : 0;
                    } else {
                        // Overall + Feedback: Cek answer pertama punya feedback
                        $firstAnswer = $submission->answers()->first();
                        $gradedAnswers = ($firstAnswer && !empty($firstAnswer->feedback)) ? $totalQuestions : 0;
                    }
                } else {
                    // Individual mode
                    if ($scoringEnabled) {
                        $gradedAnswers = $submission->answers()->whereNotNull('score')->count();
                    } else {
                        $gradedAnswers = $submission->answers()->whereNotNull('feedback')->count();
                    }
                }
            @endphp

            {{-- Progress Info untuk Scoring Essays --}}
            @if($scoringEnabled && $totalQuestions > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Grading Progress</h3>
                                <p class="text-sm text-gray-600">
                                    Mode: {{ $gradingMode === 'overall' ? 'Overall Grading' : 'Individual Questions' }}
                                </p>
                            </div>
                            <div class="text-right">
                                @php
                                    $percentage = $totalQuestions > 0 ? round(($gradedAnswers / $totalQuestions) * 100) : 0;
                                @endphp
                                <div class="text-2xl font-bold text-gray-900">{{ $gradedAnswers }}/{{ $totalQuestions }}</div>
                                <div class="text-sm text-gray-600">Questions Graded</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $percentage >= 100 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $percentage }}% Complete
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Split Layout Container --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ================================ --}}
                {{-- LEFT: Questions & Answers        --}}
                {{-- ================================ --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                @if($scoringEnabled)
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Form Penilaian
                                @else
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    Form Feedback
                                @endif
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                @if($scoringEnabled && $gradingMode === 'overall')
                                    Berikan satu nilai untuk seluruh essay
                                @elseif($scoringEnabled && $gradingMode === 'individual')
                                    Berikan nilai dan feedback untuk setiap pertanyaan
                                @else
                                    Berikan feedback untuk membantu siswa
                                @endif
                            </p>
                        </div>

                        <div class="p-4 space-y-4 max-h-[60vh] overflow-y-auto">

                            {{-- MODE 1: SCORING + OVERALL --}}
                            @if($scoringEnabled && $gradingMode === 'overall')
                                @php
                                    $totalMaxScore = $submission->content->essayQuestions()->sum('max_score') ?: 100;
                                    $firstAnswer   = $submission->answers()->first();
                                    $overallScore  = $firstAnswer ? $firstAnswer->score    : null;
                                    $overallFb     = $firstAnswer ? $firstAnswer->feedback : '';
                                @endphp

                                <div class="space-y-6">
                                    <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-purple-800">Mode Penilaian Keseluruhan</p>
                                                <p class="text-xs text-purple-600 mt-1">Berikan satu nilai total dan feedback umum.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Keseluruhan</label>
                                            <input type="number" name="overall_score" min="0" max="{{ $totalMaxScore }}"
                                                value="{{ old('overall_score', $overallScore) }}"
                                                class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" />
                                            <span class="text-xs text-gray-500">/ {{ $totalMaxScore }}</span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Feedback Keseluruhan</label>
                                            <textarea name="overall_feedback" rows="5"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">{{ old('overall_feedback', $overallFb) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            {{-- MODE 2: SCORING + INDIVIDUAL --}}
                            @elseif($scoringEnabled && $gradingMode === 'individual')
                                @if($totalQuestions > 0)
                                    @foreach($submission->content->essayQuestions()->orderBy('order')->get() as $index => $question)
                                        @php
                                            $answer = $submission->answers()->where('question_id', $question->id)->first();
                                        @endphp

                                        <div class="mb-6 p-4 border border-gray-200 rounded-lg {{ $index > 0 ? 'border-t-4 border-t-blue-200' : '' }}">
                                            <div class="mb-4">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="font-semibold text-gray-900">Pertanyaan {{ $index + 1 }}</h4>
                                                    <span class="text-xs text-gray-500">Max: {{ $question->max_score }} poin</span>
                                                </div>
                                            </div>

                                            <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                                <p class="text-blue-900">{!! nl2br(e($question->question)) !!}</p>
                                            </div>

                                            <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200 min-h-[80px]">
                                                @if($answer && $answer->answer)
                                                    <div class="prose prose-sm max-w-none text-gray-800">{!! nl2br(e($answer->answer)) !!}</div>
                                                @else
                                                    <p class="text-gray-500 italic">Tidak ada jawaban untuk pertanyaan ini.</p>
                                                @endif
                                            </div>

                                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Pertanyaan {{ $index + 1 }}</label>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number" name="scores[{{ optional($answer)->id }}]" min="0" max="{{ $question->max_score }}"
                                                            value="{{ optional($answer)->score }}"
                                                            class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                                                        <span class="text-xs text-gray-500">/ {{ $question->max_score }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Feedback Pertanyaan {{ $index + 1 }}</label>
                                                    <textarea name="feedback[{{ optional($answer)->id }}]" rows="4"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ optional($answer)->feedback }}</textarea>
                                                </div>
                                            </div>

                                            @if($answer && ($answer->score !== null || $answer->feedback))
                                                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    @if($answer->score !== null)
                                                        <p class="text-sm font-medium text-yellow-800">
                                                            Nilai saat ini: <span class="font-bold">{{ $answer->score }}</span>/{{ $question->max_score }}
                                                        </p>
                                                    @endif
                                                    @if($answer->feedback)
                                                        <p class="text-sm text-yellow-700 mt-1"><strong>Feedback:</strong> {{ $answer->feedback }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    @php $answer = $submission->answers()->first(); @endphp
                                    <div class="p-4">
                                        <h4 class="font-semibold text-gray-900 mb-3">Essay Answer</h4>
                                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 min-h-[200px]">
                                            @if($answer && $answer->answer)
                                                <div class="prose max-w-none text-gray-800">{!! nl2br(e($answer->answer)) !!}</div>
                                            @else
                                                <p class="text-gray-500 italic">No answer provided.</p>
                                            @endif
                                        </div>
                                        @if($answer && ($answer->score !== null || $answer->feedback))
                                            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                @if($answer->score !== null)
                                                    <p class="text-sm font-medium text-yellow-800">Nilai saat ini: <span class="font-bold">{{ $answer->score }}</span></p>
                                                @endif
                                                @if($answer->feedback)
                                                    <p class="text-sm text-yellow-700 mt-1"><strong>Feedback:</strong> {{ $answer->feedback }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif

                            {{-- MODE 3: NON-SCORING + OVERALL --}}
                            @else
                                <div class="space-y-6">
                                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-blue-800">Mode Feedback Keseluruhan</p>
                                                <p class="text-xs text-blue-600 mt-1">
                                                    Berikan feedback umum untuk semua {{ $totalQuestions > 0 ? $totalQuestions . ' pertanyaan' : 'essay' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $existingFeedback = '';
                                        $firstAnswer = $submission->answers()->first();
                                        if ($firstAnswer) { $existingFeedback = $firstAnswer->feedback ?? ''; }
                                    @endphp

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback untuk Seluruh Essay</label>
                                        <textarea id="overall_feedback_textarea" name="overall_feedback" rows="6"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none"
                                                placeholder="Tulis feedback umum di sini.">{{ old('overall_feedback', $existingFeedback) }}</textarea>

                                        <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
                                            <button type="button" onclick="setOverallFeedback('Excellent work! Clear structure and strong arguments.')"
                                                    class="p-3 text-left bg-green-100 text-green-800 text-sm rounded hover:bg-green-200 transition-colors border border-green-200">Excellent</button>
                                            <button type="button" onclick="setOverallFeedback('Good effort, address minor gaps in explanation and add examples.')"
                                                    class="p-3 text-left bg-blue-100 text-blue-800 text-sm rounded hover:bg-blue-200 transition-colors border border-blue-200">Good</button>
                                            <button type="button" onclick="setOverallFeedback('Needs improvement: perjelas argumen inti dan dukung dengan data/rujukan.')"
                                                    class="p-3 text-left bg-yellow-100 text-yellow-800 text-sm rounded hover:bg-yellow-200 transition-colors border border-yellow-200">Needs Improvement</button>
                                            <button type="button" onclick="setOverallFeedback('Incomplete: lengkapi bagian yang belum terjawab dan periksa kembali instruksi.')"
                                                    class="p-3 text-left bg-red-100 text-red-800 text-sm rounded hover:bg-red-200 transition-colors border border-red-200">Incomplete</button>
                                            <button type="button" onclick="setOverallFeedback('Thank you for your submission. I appreciate the effort you put into this assignment. Your ideas show promise and with more development, they will be even stronger.')"
                                                    class="p-3 text-left bg-purple-100 text-purple-800 text-sm rounded hover:bg-purple-200 transition-colors border border-purple-200">Encouraging</button>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-800 mb-2">Tips untuk Feedback Efektif:</h4>
                                        <ul class="text-xs text-gray-600 space-y-1">
                                            <li>• Sebutkan aspek positif dari jawaban siswa</li>
                                            <li>• Berikan saran spesifik untuk perbaikan</li>
                                            <li>• Gunakan bahasa yang membangun dan mendorong</li>
                                            <li>• Fokus pada isi dan pemahaman, bukan hanya grammar</li>
                                        </ul>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- ================================ --}}
                {{-- RIGHT: Grading Form               --}}
                {{-- ================================ --}}
                <div class="lg:sticky lg:top-6">
                    <form action="{{ 
                        $scoringEnabled && $gradingMode === 'overall' 
                            ? route('gradebook.store-overall-grade', $submission)
                            : (!$scoringEnabled 
                                ? route('gradebook.store-overall-feedback', $submission)
                                : route('gradebook.store-multi-grade', $submission)) 
                    }}" method="POST">
                        @csrf

                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-4 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    @if($scoringEnabled)
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Form Penilaian
                                    @else
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        Form Feedback
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    @if($scoringEnabled && $gradingMode === 'overall')
                                        Berikan satu nilai untuk seluruh essay
                                    @elseif($scoringEnabled && $gradingMode === 'individual')
                                        Berikan nilai dan feedback untuk setiap pertanyaan
                                    @else
                                        Berikan feedback untuk membantu siswa
                                    @endif
                                </p>
                            </div>

                            <div class="p-4 border-t border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        @if($scoringEnabled && $gradingMode === 'overall')
                                            <span class="text-purple-600 font-medium">Mode: Overall Grading</span>
                                        @elseif($scoringEnabled && $gradingMode === 'individual')
                                            @if(isset($gradedAnswers, $totalQuestions) && $gradedAnswers >= $totalQuestions && $totalQuestions > 0)
                                                <span class="text-green-600 font-medium">Semua sudah dinilai</span>
                                            @elseif(isset($gradedAnswers, $totalQuestions) && $totalQuestions > 0)
                                                <span class="text-yellow-600 font-medium">{{ $totalQuestions - $gradedAnswers }} belum dinilai</span>
                                            @else
                                                <span class="text-gray-600">Mode: Individual Grading</span>
                                            @endif
                                        @else
                                            <span class="text-blue-600 font-medium">Mode: Feedback Only</span>
                                        @endif
                                    </div>

                                    <button type="submit" id="saveButton"
                                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <span>
                                            @if($scoringEnabled)
                                                Simpan Penilaian
                                            @else
                                                Simpan Feedback
                                            @endif
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced JavaScript --}}
    <script>
        // Quick feedback function untuk individual mode
        function addQuickFeedback(answerId, feedbackText) {
            const textarea = document.querySelector(`textarea[name="feedback[${answerId}]"]`);
            if (textarea) {
                const currentValue = textarea.value.trim();
                if (currentValue) {
                    textarea.value = currentValue + ' ' + feedbackText;
                } else {
                    textarea.value = feedbackText;
                }
                textarea.focus();
            }
        }

        // Set overall feedback function untuk overall mode
        function setOverallFeedback(feedbackText) {
            const textarea = document.querySelector('textarea[name="overall_feedback"]');
            if (textarea) {
                textarea.value = feedbackText;
                textarea.focus();
                // Trigger change event untuk update button state
                textarea.dispatchEvent(new Event('input'));
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const scoringEnabled = {{ $scoringEnabled ? 'true' : 'false' }};
            const gradingMode = '{{ $gradingMode }}';
            
            console.log('Grading Mode:', gradingMode, 'Scoring:', scoringEnabled); // Debug
            
            if (scoringEnabled && gradingMode === 'overall') {
                // OVERALL GRADING MODE - Hanya 1 form
                console.log('Initializing OVERALL grading mode');
                
                const overallScoreInput = document.querySelector('input[name="overall_score"]');
                const overallFeedbackInput = document.querySelector('textarea[name="overall_feedback"]');
                const submitButton = document.getElementById('saveButton');
                
                if (overallScoreInput && submitButton) {
                    const originalScore = overallScoreInput.value || '';
                    const originalFeedback = overallFeedbackInput ? overallFeedbackInput.value || '' : '';
                    
                    function checkOverallChanges() {
                        const currentScore = overallScoreInput.value || '';
                        const currentFeedback = overallFeedbackInput ? overallFeedbackInput.value || '' : '';
                        
                        const hasChanges = currentScore !== originalScore || currentFeedback !== originalFeedback;
                        
                        if (hasChanges) {
                            submitButton.className = 'px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2';
                            submitButton.disabled = false;
                            submitButton.querySelector('span').textContent = 'Simpan Feedback';
                        } else {
                            submitButton.className = 'px-6 py-3 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed flex items-center space-x-2';
                            submitButton.disabled = true;
                            submitButton.querySelector('span').textContent = 'Tidak Ada Perubahan';
                        }
                    }
                    
                    feedbackInputs.forEach(input => {
                        input.addEventListener('input', checkFeedbackChanges);
                    });
                    
                    checkFeedbackChanges();
                }
            }
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+S untuk save
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitButton = document.getElementById('saveButton');
                    if (submitButton && !submitButton.disabled) {
                        submitButton.click();
                    }
                }
            });
        });
    </script>

    {{-- Custom CSS untuk improve UX --}}
    <style>
        /* Smooth transitions */
        .transition-all {
            transition: all 0.2s ease-in-out;
        }
        
        /* Better focus states */
        input:focus, textarea:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }
        
        /* Sticky positioning untuk form */
        @media (min-width: 1024px) {
            .lg\:sticky {
                position: sticky;
                top: 1.5rem;
            }
        }
        
        /* Custom scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Animation untuk progress bar */
        @keyframes progressFill {
            from {
                width: 0%;
            }
        }
        
        .bg-gradient-to-r {
            animation: progressFill 1s ease-out;
        }
        
        /* Hover effects untuk quick feedback buttons */
        .quick-feedback-btn {
            transition: all 0.15s ease;
        }
        
        .quick-feedback-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .grid {
                display: block !important;
            }
            
            .lg\:grid-cols-2 {
                grid-template-columns: none !important;
            }
        }
    </style>
</x-app-layout> {
                            submitButton.className = 'px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2';
                            submitButton.disabled = false;
                            submitButton.querySelector('span').textContent = 'Simpan Penilaian Keseluruhan';
                        } else {
                            submitButton.className = 'px-6 py-3 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed flex items-center space-x-2';
                            submitButton.disabled = true;
                            submitButton.querySelector('span').textContent = 'Tidak Ada Perubahan';
                        }
                    }
                    
                    overallScoreInput.addEventListener('input', checkOverallChanges);
                    if (overallFeedbackInput) {
                        overallFeedbackInput.addEventListener('input', checkOverallChanges);
                    }
                    
                    checkOverallChanges();
                }
                
            } else if (scoringEnabled && gradingMode === 'individual') {
                // INDIVIDUAL GRADING MODE - Multiple forms per question
                console.log('Initializing INDIVIDUAL grading mode');
                
                const scoreInputs = document.querySelectorAll('input[name^="scores"]');
                const feedbackInputs = document.querySelectorAll('textarea[name^="feedback"]');
                const submitButton = document.getElementById('saveButton');
                
                console.log('Found score inputs:', scoreInputs.length, 'feedback inputs:', feedbackInputs.length);
                
                if (submitButton) {
                    const originalValues = {};
                    
                    [...scoreInputs, ...feedbackInputs].forEach(input => {
                        originalValues[input.name] = input.value || '';
                    });
                    
                    function checkForChanges() {
                        let hasChanges = false;
                        
                        [...scoreInputs, ...feedbackInputs].forEach(input => {
                            const currentValue = input.value || '';
                            const originalValue = originalValues[input.name] || '';
                            
                            if (currentValue !== originalValue) {
                                hasChanges = true;
                            }
                        });
                        
                        if (hasChanges) {
                            submitButton.className = 'px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2';
                            submitButton.disabled = false;
                            submitButton.querySelector('span').textContent = 'Simpan Penilaian';
                        } else {
                            submitButton.className = 'px-6 py-3 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed flex items-center space-x-2';
                            submitButton.disabled = true;
                            submitButton.querySelector('span').textContent = 'Tidak Ada Perubahan';
                        }
                    }
                    
                    [...scoreInputs, ...feedbackInputs].forEach(input => {
                        input.addEventListener('input', checkForChanges);
                    });
                    
                    checkForChanges();
                }
                
            } else {
                // NON-SCORING MODE - Feedback only
                console.log('Initializing FEEDBACK-ONLY mode');
                
                const feedbackInputs = document.querySelectorAll('textarea[name^="feedback"], textarea[name="overall_feedback"]');
                const submitButton = document.getElementById('saveButton');
                
                console.log('Found feedback inputs:', feedbackInputs.length);
                
                if (feedbackInputs.length > 0 && submitButton) {
                    const originalFeedbacks = {};
                    
                    feedbackInputs.forEach(input => {
                        originalFeedbacks[input.name] = input.value || '';
                    });
                    
                    function checkFeedbackChanges() {
                        let hasChanges = false;
                        
                        feedbackInputs.forEach(input => {
                            const currentValue = input.value || '';
                            const originalValue = originalFeedbacks[input.name] || '';
                            
                            if (currentValue !== originalValue) {
                                hasChanges = true;
                            }
                        });
                        
                        if (hasChanges) {
                            submitButton.className = 'px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2';
                            submitButton.disabled = false;
                            submitButton.querySelector('span').textContent = 'Simpan Feedback';
                        } else {
                            submitButton.className = 'px-6 py-3 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed flex items-center space-x-2';
                            submitButton.disabled = true;
                            submitButton.querySelector('span').textContent = 'Tidak Ada Perubahan';
                        }
                    }
                    
                    feedbackInputs.forEach(input => {
                        input.addEventListener('input', checkFeedbackChanges);
                    });
                    
                    checkFeedbackChanges();
                }
            }
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+S untuk save
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitButton = document.getElementById('saveButton');
                    if (submitButton && !submitButton.disabled) {
                        submitButton.click();
                    }
                }
            });
        });
    </script>

    {{-- Custom CSS untuk improve UX --}}
    <style>
        /* Smooth transitions */
        .transition-all {
            transition: all 0.2s ease-in-out;
        }
        
        /* Better focus states */
        input:focus, textarea:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }
        
        /* Sticky positioning untuk form */
        @media (min-width: 1024px) {
            .lg\:sticky {
                position: sticky;
                top: 1.5rem;
            }
        }
        
        /* Custom scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Animation untuk progress bar */
        @keyframes progressFill {
            from {
                width: 0%;
            }
        }
        
        .bg-gradient-to-r {
            animation: progressFill 1s ease-out;
        }
        
        /* Hover effects untuk quick feedback buttons */
        .quick-feedback-btn {
            transition: all 0.15s ease;
        }
        
        .quick-feedback-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .grid {
                display: block !important;
            }
            
            .lg\:grid-cols-2 {
                grid-template-columns: none !important;
            }
        }
    </style>
</x-app-layout>