<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Grade Essay: {{ $submission->content->title }}
            </h2>
            <div class="flex items-center space-x-3">
                {{-- Status badge untuk scoring --}}
                @php
                    $scoringEnabled = isset($scoringEnabled) ? $scoringEnabled : ($submission->content->scoring_enabled ?? true);
                    $gradingMode = isset($gradingMode) ? $gradingMode : ($submission->content->grading_mode ?? 'individual');
                @endphp
                
                @if($scoringEnabled)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Dengan Penilaian
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Tanpa Penilaian
                    </span>
                @endif
                
                {{-- Grading Mode Badge --}}
                @if($gradingMode === 'overall')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Overall Grading
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        Individual Questions
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
                
                // Calculate graded answers based on mode
                if ($gradingMode === 'overall') {
                    if ($scoringEnabled) {
                        $firstAnswer = $submission->answers()->first();
                        $gradedAnswers = ($firstAnswer && $firstAnswer->score !== null) ? $totalQuestions : 0;
                    } else {
                        $firstAnswer = $submission->answers()->first();
                        $gradedAnswers = ($firstAnswer && !empty($firstAnswer->feedback)) ? $totalQuestions : 0;
                    }
                } else {
                    if ($scoringEnabled) {
                        $gradedAnswers = $submission->answers()->whereNotNull('score')->count();
                    } else {
                        $gradedAnswers = $submission->answers()->whereNotNull('feedback')->count();
                    }
                }
            @endphp

            {{-- Progress Info --}}
            @if($totalQuestions > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Grading Progress</h3>
                                <p class="text-sm text-gray-600">
                                    Mode: {{ $gradingMode === 'overall' ? 'Overall Grading (Bulk Assessment)' : 'Individual Question Grading' }}
                                </p>
                                @if($gradingMode === 'overall')
                                    <p class="text-xs text-purple-600 mt-1">
                                        @if($scoringEnabled)
                                            One score applies to all {{ $totalQuestions }} questions
                                        @else
                                            One feedback applies to all {{ $totalQuestions }} questions
                                        @endif
                                    </p>
                                @else
                                    <p class="text-xs text-orange-600 mt-1">
                                        @if($scoringEnabled)
                                            Each question gets individual score
                                        @else
                                            Each question gets individual feedback
                                        @endif
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                @php
                                    $percentage = $totalQuestions > 0 ? round(($gradedAnswers / $totalQuestions) * 100) : 0;
                                @endphp
                                <div class="text-2xl font-bold text-gray-900">{{ $gradedAnswers }}/{{ $totalQuestions }}</div>
                                <div class="text-sm text-gray-600">
                                    @if($gradingMode === 'overall')
                                        {{ $gradedAnswers > 0 ? 'All Questions' : 'Questions' }} Assessed
                                    @else
                                        Questions Graded
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $percentage >= 100 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $percentage }}% Complete
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- KONDISI INI YANG DIPERBAIKI: Overall mode berlaku untuk scoring dan non-scoring --}}
            @if($gradingMode === 'overall')
                {{-- OVERALL GRADING MODE (dengan atau tanpa scoring) --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Left: Questions & Answers (Read-only) --}}
                    <div class="lg:col-span-2 space-y-4">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-4 border-b border-gray-200 bg-purple-50">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Essay Questions & Answers
                                </h3>
                                <p class="text-sm text-purple-700 mt-1">
                                    @if($scoringEnabled)
                                        Review all questions and answers before giving overall score
                                    @else
                                        Review all questions and answers before giving overall feedback
                                    @endif
                                </p>
                            </div>
                            
                            <div class="p-4 space-y-6 max-h-[70vh] overflow-y-auto">
                                @if($totalQuestions > 0)
                                    @foreach($submission->content->essayQuestions()->orderBy('order')->get() as $index => $question)
                                        @php
                                            $answer = $submission->answers()->where('question_id', $question->id)->first();
                                        @endphp
                                        
                                        <div class="p-4 border border-gray-200 rounded-lg {{ $index > 0 ? 'mt-6' : '' }}">
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="font-semibold text-gray-900">Question {{ $index + 1 }}</h4>
                                                <span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">
                                                    @if($scoringEnabled)
                                                        Max: {{ $question->max_score }} points
                                                    @else
                                                        Feedback Required
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400 mb-3">
                                                <p class="text-blue-900">{!! nl2br(e($question->question)) !!}</p>
                                            </div>
                                            
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 min-h-[80px]">
                                                @if($answer && $answer->answer)
                                                    <div class="prose prose-sm max-w-none text-gray-800">{!! nl2br(e($answer->answer)) !!}</div>
                                                @else
                                                    <p class="text-gray-500 italic">No answer provided for this question.</p>
                                                @endif
                                            </div>
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
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right: Overall Assessment Form --}}
                    <div class="lg:sticky lg:top-6">
                        <form action="{{ 
                            $scoringEnabled 
                                ? route('gradebook.store-overall-grade', $submission)
                                : route('gradebook.store-overall-feedback', $submission)
                        }}" method="POST">
                            @csrf

                            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                                <div class="p-4 border-b border-gray-200 bg-purple-50">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Overall Assessment
                                    </h3>
                                    <p class="text-sm text-purple-700 mt-1">
                                        @if($scoringEnabled)
                                            Give one score for the entire essay ({{ $totalQuestions }} questions)
                                        @else
                                            Give overall feedback for the entire essay ({{ $totalQuestions }} questions)
                                        @endif
                                    </p>
                                </div>

                                <div class="p-4 space-y-4">
                                    @php
                                        $totalMaxScore = $submission->content->essayQuestions()->sum('max_score') ?: 100;
                                        $firstAnswer = $submission->answers()->first();
                                        $overallScore = $firstAnswer ? $firstAnswer->score : null;
                                        $overallFeedback = $firstAnswer ? $firstAnswer->feedback : '';
                                    @endphp

                                    @if($scoringEnabled)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Overall Score</label>
                                            <div class="relative">
                                                <input type="number" name="overall_score" min="0" max="{{ $totalMaxScore }}"
                                                    value="{{ old('overall_score', $overallScore) }}"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-lg font-semibold" 
                                                    placeholder="Enter total score" />
                                                <div class="absolute right-3 top-3 text-gray-500">/ {{ $totalMaxScore }}</div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">This score applies to all {{ $totalQuestions }} questions</p>
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            @if($scoringEnabled)
                                                Overall Feedback
                                            @else
                                                Feedback for Entire Essay
                                            @endif
                                        </label>
                                        <textarea name="overall_feedback" rows="8"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 resize-none"
                                                placeholder="Provide comprehensive feedback for the entire essay...">{{ old('overall_feedback', $overallFeedback) }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">This feedback applies to the entire submission</p>
                                    </div>

                                    {{-- Quick Feedback Templates --}}
                                    <div class="border-t pt-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Quick Feedback Templates:</h4>
                                        <div class="grid grid-cols-1 gap-2">
                                            <button type="button" onclick="setFeedback('Excellent work! Clear structure, strong arguments, and comprehensive coverage of all questions.')"
                                                    class="p-2 text-left bg-green-100 text-green-800 text-xs rounded hover:bg-green-200 transition-colors">
                                                Excellent Overall
                                            </button>
                                            <button type="button" onclick="setFeedback('Good effort overall. Most questions answered well, but some areas need more detail and examples.')"
                                                    class="p-2 text-left bg-blue-100 text-blue-800 text-xs rounded hover:bg-blue-200 transition-colors">
                                                Good Overall
                                            </button>
                                            <button type="button" onclick="setFeedback('Needs improvement. Please review the questions more carefully and provide more thorough answers.')"
                                                    class="p-2 text-left bg-yellow-100 text-yellow-800 text-xs rounded hover:bg-yellow-200 transition-colors">
                                                Needs Improvement
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Current Status --}}
                                    @if($overallScore !== null || $overallFeedback)
                                        <div class="border-t pt-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Current Assessment:</h4>
                                            @if($overallScore !== null)
                                                <p class="text-sm text-green-700">Score: <span class="font-bold">{{ $overallScore }}</span>/{{ $totalMaxScore }}</p>
                                            @endif
                                            @if($overallFeedback)
                                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($overallFeedback, 100) }}</p>
                                            @endif
                                        </div>
                                    @endif

                                    <button type="submit" class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <span>
                                            @if($scoringEnabled)
                                                Save Overall Grade
                                            @else
                                                Save Overall Feedback
                                            @endif
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            @else
                {{-- INDIVIDUAL GRADING MODE --}}
                <form action="{{ route('gradebook.store-multi-grade', $submission) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        @if($totalQuestions > 0)
                            @foreach($submission->content->essayQuestions()->orderBy('order')->get() as $index => $question)
                                @php
                                    $answer = $submission->answers()->where('question_id', $question->id)->first();
                                @endphp
                                
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                                <span class="bg-orange-100 text-orange-800 text-sm font-medium px-2.5 py-0.5 rounded-full mr-3">
                                                    {{ $index + 1 }}
                                                </span>
                                                Question {{ $index + 1 }} of {{ $totalQuestions }}
                                            </h4>
                                            @if($answer && (($scoringEnabled && $answer->score !== null) || (!$scoringEnabled && $answer->feedback)))
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    @if($scoringEnabled)
                                                        Graded: {{ $answer->score }}/{{ $question->max_score }}
                                                    @else
                                                        Feedback Given
                                                    @endif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Pending
                                                </span>
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
                                                    <div class="prose max-w-none">{!! nl2br(e($answer->answer)) !!}</div>
                                                @else
                                                    <p class="text-gray-500 italic">No answer provided for this question.</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($answer)
                                            {{-- Individual Grading Section --}}
                                            <div class="border-t pt-6">
                                                <h5 class="font-medium text-gray-900 mb-4 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Individual Assessment for Question {{ $index + 1 }}
                                                </h5>
                                                
                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                    @if($scoringEnabled)
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
                                                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg"
                                                                       placeholder="Enter score">
                                                                <div class="absolute right-3 top-3 text-gray-500 text-sm">
                                                                    / {{ $question->max_score }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    <div class="{{ $scoringEnabled ? '' : 'lg:col-span-2' }}">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Feedback @if(!$scoringEnabled)(Required)@else(Optional)@endif
                                                        </label>
                                                        <textarea name="feedback[{{ $answer->id }}]" 
                                                                  rows="4"
                                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                                  placeholder="Provide specific feedback for this answer..."
                                                                  @if(!$scoringEnabled) required @endif>{{ $answer->feedback }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- Fallback for legacy essays --}}
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Essay Answer</h4>
                                    
                                    @php $answer = $submission->answers()->first(); @endphp
                                    
                                    <div class="mb-6 p-4 bg-gray-50 border border-gray-300 rounded-lg min-h-[200px]">
                                        @if($answer && $answer->answer)
                                            {!! nl2br(e($answer->answer)) !!}
                                        @else
                                            <p class="text-gray-500 italic">No answer provided.</p>
                                        @endif
                                    </div>
                                    
                                    @if($answer)
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            @if($scoringEnabled)
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Score</label>
                                                    <input type="number" name="scores[{{ $answer->id }}]" min="0" max="100" 
                                                           value="{{ $answer->score }}" 
                                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                                </div>
                                            @endif
                                            <div class="{{ $scoringEnabled ? '' : 'lg:col-span-2' }}">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
                                                <textarea name="feedback[{{ $answer->id }}]" rows="4" 
                                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ $answer->feedback }}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        {{-- Submit Button --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        @if($gradedAnswers >= $totalQuestions && $totalQuestions > 0)
                                            <span class="text-green-600 font-medium flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                All questions assessed individually
                                            </span>
                                        @else
                                            <span class="text-yellow-600 font-medium flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $totalQuestions - $gradedAnswers }} questions still need individual assessment
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <button type="submit" 
                                            class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <span>
                                            @if($scoringEnabled)
                                                Save Individual Grades
                                            @else
                                                Save Individual Feedback
                                            @endif
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // For Overall Grading Mode
        function setFeedback(feedbackText) {
            const textarea = document.querySelector('textarea[name="overall_feedback"]');
            if (textarea) {
                textarea.value = feedbackText;
                textarea.focus();
            }
        }

        // For Individual Grading Mode  
        function addFeedback(answerId, feedbackText) {
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

        document.addEventListener('DOMContentLoaded', function() {
            const gradingMode = '{{ $gradingMode }}';
            const scoringEnabled = {{ $scoringEnabled ? 'true' : 'false' }};
            
            // Add visual indicators for changed inputs
            const inputs = document.querySelectorAll('input[type="number"], textarea');
            inputs.forEach(input => {
                const originalValue = input.value;
                input.addEventListener('input', function() {
                    if (this.value !== originalValue) {
                        this.style.borderColor = '#f59e0b';
                        this.style.boxShadow = '0 0 0 1px #f59e0b';
                    } else {
                        this.style.borderColor = '';
                        this.style.boxShadow = '';
                    }
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitButton = document.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.click();
                    }
                }
            });
        });
    </script>

    {{-- Custom CSS --}}
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
        
        /* Sticky positioning */
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
        
        /* Progress bar animation */
        .bg-gradient-to-r {
            animation: progressFill 1s ease-out;
        }
        
        @keyframes progressFill {
            from { width: 0%; }
        }
        
        /* Print styles */
        @media print {
            .no-print { display: none !important; }
            .grid { display: block !important; }
            .lg\:grid-cols-2 { grid-template-columns: none !important; }
        }

        /* Highlight unsaved changes */
        .changed-input {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 1px #f59e0b !important;
        }
    </style>
</x-app-layout>