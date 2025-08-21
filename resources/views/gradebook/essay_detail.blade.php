<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Grade Essay: {{ $submission->content->title }}
            </h2>
            <div class="flex items-center space-x-3">
                {{-- Status badge untuk scoring --}}
                @if($submission->content->scoring_enabled)
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Submission Header --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $submission->user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $submission->user->email }}</p>
                            <p class="text-sm text-gray-600">Submitted: {{ $submission->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        <div class="text-right">
                            @if($submission->content->scoring_enabled)
                                @php
                                    $totalQuestions = $submission->content->essayQuestions()->count();
                                    $gradedAnswers = $submission->answers()->whereNotNull('score')->count();
                                    $percentage = $totalQuestions > 0 ? round(($gradedAnswers / $totalQuestions) * 100) : 0;
                                @endphp
                                <div class="text-2xl font-bold text-gray-900">{{ $gradedAnswers }}/{{ $totalQuestions }}</div>
                                <div class="text-sm text-gray-600">Questions Graded</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $percentage >= 100 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $percentage }}% Complete
                                    </span>
                                </div>
                            @else
                                <div class="text-2xl font-bold text-blue-600">Dikumpulkan</div>
                                <div class="text-sm text-gray-600">Essay Tanpa Penilaian</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        Berhasil Dikumpulkan
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Progress Bar - hanya tampil jika scoring enabled --}}
                    @if($submission->content->scoring_enabled)
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Grading Form --}}
            <form action="{{ route('gradebook.store-multi-grade', $submission) }}" method="POST">
                @csrf
                
                @if($submission->content->essayQuestions()->count() > 0)
                    {{-- Multi-Question System --}}
                    @foreach($submission->content->essayQuestions()->orderBy('order')->get() as $index => $question)
                        @php
                            $answer = $submission->answers()->where('question_id', $question->id)->first();
                        @endphp
                        
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        Question {{ $index + 1 }} of {{ $submission->content->essayQuestions()->count() }}
                                    </h4>
                                    @if($submission->content->scoring_enabled)
                                        @if($answer && $answer->score !== null)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Graded: {{ $answer->score }}/{{ $question->max_score }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                Not Graded
                                            </span>
                                        @endif
                                    @else
                                        @if($answer && $answer->feedback)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                Ada Catatan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                Belum Ada Catatan
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                
                                {{-- Question Text --}}
                                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                                    <h5 class="font-medium text-blue-900 mb-2">Question:</h5>
                                    <div class="text-blue-800">{!! nl2br(e($question->question)) !!}</div>
                                </div>
                                
                                {{-- Student Answer --}}
                                <div class="mb-6">
                                    <h5 class="font-medium text-gray-900 mb-3">Student Answer:</h5>
                                    <div class="p-4 bg-gray-50 border border-gray-300 rounded-lg min-h-[120px]">
                                        @if($answer && $answer->answer)
                                            <div class="prose max-w-none">
                                                {!! nl2br(e($answer->answer)) !!}
                                            </div>
                                        @else
                                            <p class="text-gray-500 italic">No answer provided for this question.</p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($answer)
                                    {{-- Grading Section - berbeda untuk scoring enabled/disabled --}}
                                    <div class="border-t pt-6">
                                        @if($submission->content->scoring_enabled)
                                            <h5 class="font-medium text-gray-900 mb-4">Grade This Answer:</h5>
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                {{-- Score Input --}}
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Score (0 - {{ $question->max_score }})
                                                    </label>
                                                    <div class="relative">
                                                        <input type="number" 
                                                               name="scores[{{ $answer->id }}]" 
                                                               min="0" 
                                                               max="{{ $question->max_score }}" 
                                                               value="{{ $answer->score }}"
                                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                                                               placeholder="Enter score">
                                                        <div class="absolute right-3 top-3 text-gray-500 text-sm">
                                                            / {{ $question->max_score }}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Feedback Input --}}
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        Feedback (Optional)
                                                    </label>
                                                    <textarea name="feedback[{{ $answer->id }}]" 
                                                              rows="4"
                                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                              placeholder="Provide specific feedback for this answer...">{{ $answer->feedback }}</textarea>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Feedback Only untuk essay tanpa scoring --}}
                                            <h5 class="font-medium text-gray-900 mb-4">Catatan untuk Jawaban Ini:</h5>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Catatan/Feedback (Optional)
                                                </label>
                                                <textarea name="feedback[{{ $answer->id }}]" 
                                                          rows="4"
                                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                          placeholder="Berikan catatan atau feedback untuk jawaban ini...">{{ $answer->feedback }}</textarea>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                @else
                    {{-- Fallback for essays without questions --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Essay Answer</h4>
                            
                            @php
                                $answer = $submission->answers()->first();
                            @endphp
                            
                            <div class="mb-6 p-4 bg-gray-50 border border-gray-300 rounded-lg min-h-[200px]">
                                @if($answer && $answer->answer)
                                    {!! nl2br(e($answer->answer)) !!}
                                @else
                                    <p class="text-gray-500 italic">No answer provided.</p>
                                @endif
                            </div>
                            
                            @if($answer)
                                @if($submission->content->scoring_enabled)
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Score (0-100)</label>
                                            <input type="number" 
                                                   name="scores[{{ $answer->id }}]" 
                                                   min="0" 
                                                   max="100" 
                                                   value="{{ $answer->score }}"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
                                            <textarea name="feedback[{{ $answer->id }}]" 
                                                      rows="4"
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ $answer->feedback }}</textarea>
                                        </div>
                                    </div>
                                @else
                                    {{-- Feedback only untuk essay tanpa scoring --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan/Feedback</label>
                                        <textarea name="feedback[{{ $answer->id }}]" 
                                                  rows="4"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Berikan catatan untuk essay ini...">{{ $answer->feedback }}</textarea>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                @if($submission->content->scoring_enabled)
                                    @if($gradedAnswers >= $totalQuestions && $totalQuestions > 0)
                                        <span class="text-green-600 font-medium">Semua pertanyaan sudah dinilai</span>
                                    @else
                                        <span class="text-yellow-600 font-medium">{{ $totalQuestions - $gradedAnswers }} pertanyaan belum dinilai</span>
                                    @endif
                                @else
                                    <span class="text-blue-600 font-medium">Essay tanpa penilaian - catatan bersifat opsional</span>
                                @endif
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="submit" 
                                        id="saveButton"
                                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    @if($submission->content->scoring_enabled)
                                        Simpan Penilaian
                                    @else
                                        Simpan Catatan
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript for enhanced UX - dimodifikasi untuk scoring optional --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scoringEnabled = {{ $submission->content->scoring_enabled ? 'true' : 'false' }};
            
            if (scoringEnabled) {
                const scoreInputs = document.querySelectorAll('input[name^="scores"]');
                const submitButton = document.getElementById('saveButton');
                const originalScores = {};
                
                // Store original scores
                scoreInputs.forEach(input => {
                    originalScores[input.name] = input.value || '';
                });
                
                function checkForChanges() {
                    let hasChanges = false;
                    
                    scoreInputs.forEach(input => {
                        const currentValue = input.value || '';
                        const originalValue = originalScores[input.name] || '';
                        
                        if (currentValue !== originalValue) {
                            hasChanges = true;
                        }
                    });
                    
                    // Update button state based on changes
                    if (hasChanges) {
                        submitButton.textContent = 'Simpan Penilaian';
                        submitButton.disabled = false;
                        submitButton.className = 'px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors';
                    } else {
                        submitButton.textContent = 'Selesai Menilai';
                        submitButton.disabled = true;
                        submitButton.className = 'px-6 py-3 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed';
                    }
                }
                
                scoreInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        // Visual feedback for individual questions
                        const container = this.closest('.bg-white');
                        const statusBadge = container.querySelector('.bg-red-100, .bg-green-100');
                        
                        if (this.value && this.value.trim() !== '') {
                            if (statusBadge) {
                                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                                statusBadge.innerHTML = 'Graded: ' + this.value + '/' + this.getAttribute('max');
                            }
                        } else {
                            if (statusBadge) {
                                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
                                statusBadge.innerHTML = 'Not Graded';
                            }
                        }
                        
                        checkForChanges();
                        updateOverallProgress();
                    });
                });
                
                function updateOverallProgress() {
                    const totalQuestions = {{ $totalQuestions ?? 0 }};
                    const gradedInputs = Array.from(scoreInputs).filter(input => 
                        input.value && input.value.trim() !== ''
                    );
                    const gradedCount = gradedInputs.length;
                    const percentage = totalQuestions > 0 ? Math.round((gradedCount / totalQuestions) * 100) : 0;
                    
                    // Update progress bar
                    const progressBar = document.querySelector('.bg-gradient-to-r');
                    if (progressBar) {
                        progressBar.style.width = percentage + '%';
                    }
                    
                    // Update percentage text
                    const percentageText = document.querySelector('.inline-flex.items-center.px-3.py-1.rounded-full');
                    if (percentageText && percentageText.textContent.includes('Complete')) {
                        percentageText.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                            percentage >= 100 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                        }`;
                        percentageText.textContent = percentage + '% Complete';
                    }
                    
                    // Update count
                    const countText = document.querySelector('.text-2xl.font-bold');
                    if (countText && countText.textContent.includes('/')) {
                        countText.textContent = gradedCount + '/' + totalQuestions;
                    }
                }
                
                // Initial check
                checkForChanges();
            } else {
                // For non-scoring essays, handle feedback changes
                const feedbackInputs = document.querySelectorAll('textarea[name^="feedback"]');
                const submitButton = document.getElementById('saveButton');
                const originalFeedbacks = {};
                
                // Store original feedback
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
                        submitButton.textContent = 'Simpan Catatan';
                        submitButton.disabled = false;
                        submitButton.className = 'px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors';
                    } else {
                        submitButton.textContent = 'Sudah Memberikan Feedback';
                        submitButton.disabled = true;
                        submitButton.className = 'px-6 py-3 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed';
                    }
                }
                
                feedbackInputs.forEach(input => {
                    input.addEventListener('input', checkFeedbackChanges);
                });
                
                // Initial check
                checkFeedbackChanges();
            }
        });
    </script>
</x-app-layout>