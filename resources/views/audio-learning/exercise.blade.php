@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('audio-learning.index') }}" class="text-blue-600 hover:text-blue-700">Audio Learning</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('audio-learning.lesson', $lesson->id) }}" class="text-blue-600 hover:text-blue-700 ml-1 md:ml-2">{{ $lesson->title }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">{{ $exercise->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Exercise Section -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <!-- Exercise Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $exercise->title }}</h1>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $exercise->exercise_type)) }}
                            </span>
                            <span class="text-sm text-gray-600">{{ $exercise->points }} points</span>
                        </div>
                    </div>

                    @if($userProgress && $userProgress->completed)
                        <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                            ✓ Completed
                        </div>
                    @endif
                </div>

                <!-- Audio Player for Exercise -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="audio-exercise-player" data-src="{{ $lesson->audio_url }}"
                         data-start="{{ $exercise->play_from_seconds }}"
                         data-end="{{ $exercise->play_to_seconds }}">
                        <audio id="exerciseAudio" class="w-full mb-4" controls>
                            <source src="{{ $lesson->audio_url }}" type="audio/mpeg">
                            <source src="{{ $lesson->audio_url }}" type="audio/wav">
                        </audio>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button id="playExerciseBtn" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-3 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                    </svg>
                                </button>

                                <button id="replaySegmentBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Replay Segment
                                </button>

                                @if($exercise->audio_cue)
                                    <div class="text-sm text-gray-600 italic">
                                        {{ $exercise->audio_cue }}
                                    </div>
                                @endif
                            </div>

                            <div class="text-sm text-gray-600">
                                @if($exercise->play_from_seconds || $exercise->play_to_seconds)
                                    Segment: {{ $exercise->play_from_seconds }}s - {{ $exercise->play_to_seconds }}s
                                @else
                                    Full Audio
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Question:</h3>
                    <p class="text-gray-700">{{ $exercise->question }}</p>
                </div>

                <!-- Answer Section -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <form id="exerciseForm" data-exercise-type="{{ $exercise->exercise_type }}">
                        @csrf

                        @if($exercise->exercise_type === 'multiple_choice')
                            <!-- Multiple Choice -->
                            <h4 class="font-medium text-gray-800 mb-4">Choose the correct answer:</h4>
                            <div class="space-y-3">
                                @foreach($exercise->options as $index => $option)
                                    <label class="flex items-center p-3 bg-white rounded-lg border hover:border-blue-300 cursor-pointer transition-colors">
                                        <input type="radio" name="answer" value="{{ $option }}" class="mr-3">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($exercise->exercise_type === 'fill_blank')
                            <!-- Fill in the Blank -->
                            <h4 class="font-medium text-gray-800 mb-4">Type your answer:</h4>
                            <input type="text" name="answer" id="textAnswer"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter your answer here...">

                        @elseif($exercise->exercise_type === 'speech_response')
                            <!-- Speech Response -->
                            <h4 class="font-medium text-gray-800 mb-4">Record your answer:</h4>

                            <div class="text-center">
                                <button type="button" id="speechBtn" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors mb-4">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 016 0v6a3 3 0 01-3 3z"></path>
                                    </svg>
                                    <span id="speechBtnText">Click to Record</span>
                                </button>

                                <div id="speechStatus" class="text-sm text-gray-600 mb-4" style="display: none;">
                                    <div class="flex items-center justify-center">
                                        <div class="animate-pulse bg-red-500 rounded-full w-3 h-3 mr-2"></div>
                                        Listening...
                                    </div>
                                </div>

                                <div id="speechTranscript" class="bg-white p-4 rounded-lg border border-gray-300 text-left" style="display: none;">
                                    <h5 class="font-medium text-gray-800 mb-2">Your speech was recognized as:</h5>
                                    <p id="transcriptText" class="text-gray-700 italic"></p>
                                    <div id="confidenceLevel" class="text-sm text-gray-500 mt-2"></div>
                                </div>
                            </div>

                            <input type="hidden" name="transcript" id="speechTranscriptInput">
                            <input type="hidden" name="confidence" id="speechConfidenceInput">

                        @elseif($exercise->exercise_type === 'comprehension')
                            <!-- Comprehension -->
                            <h4 class="font-medium text-gray-800 mb-4">Write your answer:</h4>
                            <textarea name="answer" id="comprehensionAnswer" rows="4"
                                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Write your detailed answer here..."></textarea>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between mt-6">
                            <div id="exerciseResult" class="flex-1 mr-4" style="display: none;">
                                <div id="resultMessage" class="p-3 rounded-lg"></div>
                            </div>

                            <button type="submit" id="submitBtn" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                Submit Answer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Next Exercise Button -->
                <div class="mt-6 text-center">
                    @php
                        $nextExercise = $lesson->exercises->where('sort_order', '>', $exercise->sort_order)->first();
                    @endphp

                    @if($nextExercise)
                        <a href="{{ route('audio-learning.exercise', [$lesson->id, $nextExercise->id]) }}"
                           id="nextExerciseBtn"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-block"
                           style="display: none;">
                            Next Exercise
                        </a>
                    @else
                        <a href="{{ route('audio-learning.lesson', $lesson->id) }}"
                           id="backToLessonBtn"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-block"
                           style="display: none;">
                            Back to Lesson
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Exercise Progress -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Exercise Progress</h3>

                @if($userProgress)
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Attempts</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $userProgress->attempts_count }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-600 mb-1">Current Score</div>
                            <div class="text-2xl font-bold {{ $userProgress->completed ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $userProgress->score }}/{{ $exercise->points }}
                            </div>
                        </div>

                        @if($userProgress->completed)
                            <div class="bg-green-100 text-green-800 p-3 rounded-lg text-sm text-center font-medium">
                                ✓ Exercise Completed!
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Complete the exercise to see your progress</p>
                @endif
            </div>

            <!-- Exercise Navigation -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">All Exercises</h3>

                <div class="space-y-2">
                    @foreach($lesson->exercises as $ex)
                        <a href="{{ route('audio-learning.exercise', [$lesson->id, $ex->id]) }}"
                           class="block p-3 rounded-lg border transition-colors
                                  {{ $ex->id === $exercise->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-800">{{ $ex->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $ex->points }} points</div>
                                </div>

                                @auth
                                    @if($progress = $ex->userProgressFor(auth()->id()))
                                        @if($progress->completed)
                                            <div class="text-green-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="text-yellow-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('exerciseAudio');
    const playBtn = document.getElementById('playExerciseBtn');
    const replayBtn = document.getElementById('replaySegmentBtn');
    const form = document.getElementById('exerciseForm');
    const exerciseType = form.dataset.exerciseType;
    const resultDiv = document.getElementById('exerciseResult');
    const resultMessage = document.getElementById('resultMessage');
    const nextBtn = document.getElementById('nextExerciseBtn');
    const backBtn = document.getElementById('backToLessonBtn');

    // Audio segment settings
    const startTime = {{ $exercise->play_from_seconds ?? 0 }};
    const endTime = {{ $exercise->play_to_seconds ?? 'null' }};

    let recognition = null;
    let isRecording = false;

    // Initialize speech recognition if supported
    if (exerciseType === 'speech_response' && 'webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';

        setupSpeechRecognition();
    } else if (exerciseType === 'speech_response') {
        document.getElementById('speechBtn').innerHTML = '⚠️ Speech recognition not supported in this browser';
        document.getElementById('speechBtn').disabled = true;
    }

    // Audio controls
    playBtn.addEventListener('click', function() {
        if (audio.paused) {
            audio.currentTime = startTime;
            audio.play();
            updatePlayButton(false);
        } else {
            audio.pause();
            updatePlayButton(true);
        }
    });

    replayBtn.addEventListener('click', function() {
        audio.currentTime = startTime;
        audio.play();
        updatePlayButton(false);
    });

    // Handle audio time updates
    audio.addEventListener('timeupdate', function() {
        if (endTime && audio.currentTime >= endTime) {
            audio.pause();
            updatePlayButton(true);
        }
    });

    audio.addEventListener('ended', function() {
        updatePlayButton(true);
    });

    function updatePlayButton(isPaused) {
        if (isPaused) {
            playBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path></svg>';
        } else {
            playBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }
    }

    // Speech recognition setup
    function setupSpeechRecognition() {
        const speechBtn = document.getElementById('speechBtn');
        const speechStatus = document.getElementById('speechStatus');
        const speechTranscript = document.getElementById('speechTranscript');
        const transcriptText = document.getElementById('transcriptText');
        const confidenceDiv = document.getElementById('confidenceLevel');
        const transcriptInput = document.getElementById('speechTranscriptInput');
        const confidenceInput = document.getElementById('speechConfidenceInput');

        speechBtn.addEventListener('click', function() {
            if (!isRecording) {
                startRecording();
            } else {
                stopRecording();
            }
        });

        function startRecording() {
            isRecording = true;
            speechBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
            speechBtn.classList.add('bg-gray-500', 'hover:bg-gray-600');
            document.getElementById('speechBtnText').textContent = 'Recording... Click to Stop';
            speechStatus.style.display = 'block';
            speechTranscript.style.display = 'none';

            recognition.start();
        }

        function stopRecording() {
            isRecording = false;
            speechBtn.classList.remove('bg-gray-500', 'hover:bg-gray-600');
            speechBtn.classList.add('bg-red-500', 'hover:bg-red-600');
            document.getElementById('speechBtnText').textContent = 'Click to Record';
            speechStatus.style.display = 'none';

            recognition.stop();
        }

        recognition.onresult = function(event) {
            const result = event.results[0];
            const transcript = result[0].transcript;
            const confidence = result[0].confidence;

            transcriptText.textContent = transcript;
            confidenceDiv.textContent = `Confidence: ${Math.round(confidence * 100)}%`;
            transcriptInput.value = transcript;
            confidenceInput.value = confidence;

            speechTranscript.style.display = 'block';
            stopRecording();
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            stopRecording();
            alert('Speech recognition error: ' + event.error);
        };

        recognition.onend = function() {
            if (isRecording) {
                stopRecording();
            }
        };
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let answer = '';
        let type = 'text';

        if (exerciseType === 'multiple_choice') {
            const selectedOption = form.querySelector('input[name="answer"]:checked');
            if (!selectedOption) {
                alert('Please select an answer');
                return;
            }
            answer = selectedOption.value;
            type = 'multiple_choice';
        } else if (exerciseType === 'fill_blank') {
            answer = document.getElementById('textAnswer').value.trim();
            if (!answer) {
                alert('Please enter your answer');
                return;
            }
        } else if (exerciseType === 'speech_response') {
            answer = document.getElementById('speechTranscriptInput').value;
            if (!answer) {
                alert('Please record your speech first');
                return;
            }
            type = 'speech';
        } else if (exerciseType === 'comprehension') {
            answer = document.getElementById('comprehensionAnswer').value.trim();
            if (!answer) {
                alert('Please write your answer');
                return;
            }
        }

        // Submit the answer
        const submitUrl = exerciseType === 'speech_response'
            ? `{{ route('audio-learning.submit-speech', [$lesson->id, $exercise->id]) }}`
            : `{{ route('audio-learning.submit-answer', [$lesson->id, $exercise->id]) }}`;

        const submitData = {
            answer: answer,
            type: type
        };

        if (exerciseType === 'speech_response') {
            submitData.transcript = answer;
            submitData.confidence = document.getElementById('speechConfidenceInput').value;
        }

        fetch(submitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(submitData)
        })
        .then(response => response.json())
        .then(data => {
            showResult(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting your answer');
        });
    });

    function showResult(data) {
        resultDiv.style.display = 'block';

        if (data.correct) {
            resultMessage.className = 'p-3 rounded-lg bg-green-100 text-green-800';
            resultMessage.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <strong>Correct!</strong> You earned ${data.points} points.
                </div>
            `;

            // Show next exercise button
            if (nextBtn) {
                nextBtn.style.display = 'inline-block';
            } else if (backBtn) {
                backBtn.style.display = 'inline-block';
            }
        } else {
            resultMessage.className = 'p-3 rounded-lg bg-red-100 text-red-800';
            let incorrectHtml = `
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <strong>Incorrect.</strong> Try again!
                </div>
            `;

            if (data.correct_answer) {
                incorrectHtml += `<div class="text-sm mt-2">Correct answer: <strong>${data.correct_answer}</strong></div>`;
            }

            resultMessage.innerHTML = incorrectHtml;
        }

        if (exerciseType === 'speech_response' && data.confidence) {
            const confidenceInfo = document.createElement('div');
            confidenceInfo.className = 'text-sm mt-2 text-gray-600';
            confidenceInfo.textContent = `Speech confidence: ${Math.round(data.confidence * 100)}%`;
            resultMessage.appendChild(confidenceInfo);
        }
    }
});
</script>
@endpush
@endsection