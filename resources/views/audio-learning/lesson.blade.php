@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('audio-learning.index') }}" class="text-blue-600 hover:text-blue-700">
                        Audio Learning
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">{{ $lesson->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Audio Player Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <!-- Lesson Info -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h1 class="text-2xl font-bold text-gray-800">{{ $lesson->title }}</h1>
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                                   {{ $lesson->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' :
                                      ($lesson->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' :
                                       'bg-red-100 text-red-800') }}">
                            {{ ucfirst($lesson->difficulty_level) }}
                        </span>
                    </div>
                    @if($lesson->description)
                        <p class="text-gray-600 mb-4">{{ $lesson->description }}</p>
                    @endif
                </div>

                <!-- Audio Player -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="audio-player" data-src="{{ $lesson->audio_url }}" data-lesson-id="{{ $lesson->id }}">
                        <audio id="audioPlayer" class="w-full mb-4" controls>
                            <source src="{{ $lesson->audio_url }}" type="audio/mpeg">
                            <source src="{{ $lesson->audio_url }}" type="audio/wav">
                            Your browser does not support the audio element.
                        </audio>

                        <!-- Custom Audio Controls -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <button id="playPauseBtn" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-3 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                    </svg>
                                </button>

                                <button id="rewindBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full p-2 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z"></path>
                                    </svg>
                                </button>

                                <button id="forwardBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full p-2 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z"></path>
                                    </svg>
                                </button>

                                <div class="flex items-center space-x-2">
                                    <label for="speedControl" class="text-sm text-gray-600">Speed:</label>
                                    <select id="speedControl" class="text-sm border border-gray-300 rounded px-2 py-1">
                                        <option value="0.5">0.5x</option>
                                        <option value="0.75">0.75x</option>
                                        <option value="1" selected>1x</option>
                                        <option value="1.25">1.25x</option>
                                        <option value="1.5">1.5x</option>
                                        <option value="2">2x</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-sm text-gray-600">
                                <span id="currentTime">00:00</span> / <span id="totalTime">{{ $lesson->formatted_duration }}</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                            <div id="progressBar" class="bg-blue-500 h-2 rounded-full" style="width: 0%"></div>
                        </div>

                        <!-- Loop Controls -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" id="loopCheckbox" class="mr-2">
                                    <span class="text-sm text-gray-600">Loop</span>
                                </label>

                                <div class="flex items-center space-x-2">
                                    <label for="loopStart" class="text-sm text-gray-600">Loop from:</label>
                                    <input type="number" id="loopStart" class="w-16 text-sm border border-gray-300 rounded px-2 py-1" min="0" value="0">
                                    <span class="text-sm text-gray-600">to</span>
                                    <input type="number" id="loopEnd" class="w-16 text-sm border border-gray-300 rounded px-2 py-1" min="0" value="{{ $lesson->duration_seconds ?? 60 }}">
                                    <span class="text-sm text-gray-600">seconds</span>
                                </div>
                            </div>

                            <button id="transcriptBtn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Show Transcript
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Transcript Section (Initially Hidden) -->
                @if($lesson->transcript)
                    <div id="transcriptSection" class="bg-blue-50 rounded-lg p-6 mb-6" style="display: none;">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">Transcript</h3>
                        <div class="text-gray-700 leading-relaxed">
                            {!! nl2br(e($lesson->transcript)) !!}
                        </div>
                    </div>
                @endif

                <!-- Exercise Navigation -->
                @if($lesson->exercises->count() > 0)
                    <div class="bg-yellow-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-4">Interactive Exercises</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($lesson->exercises as $exercise)
                                <a href="{{ route('audio-learning.exercise', [$lesson->id, $exercise->id]) }}"
                                   class="bg-white border border-yellow-200 rounded-lg p-4 hover:border-yellow-300 transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-800 group-hover:text-blue-600">{{ $exercise->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ ucfirst(str_replace('_', ' ', $exercise->exercise_type)) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-700">{{ $exercise->points }} pts</div>
                                            @auth
                                                @if($progress = $exercise->userProgressFor(auth()->id()))
                                                    @if($progress->completed)
                                                        <div class="text-xs text-green-600 mt-1">✓ Completed</div>
                                                    @else
                                                        <div class="text-xs text-yellow-600 mt-1">In Progress</div>
                                                    @endif
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Progress Card -->
            @auth
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Progress</h3>
                    <div id="progressInfo">
                        @if($userProgress)
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Overall Progress</span>
                                        <span id="progressPercentage">{{ $userProgress->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $userProgress->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 pt-3 border-t">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600" id="currentScore">{{ $userProgress->score }}</div>
                                        <div class="text-xs text-gray-500">Current Score</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-gray-600" id="maxScore">{{ $userProgress->max_score }}</div>
                                        <div class="text-xs text-gray-500">Max Score</div>
                                    </div>
                                </div>

                                @if($userProgress->started_at)
                                    <div class="text-xs text-gray-500 pt-2 border-t">
                                        Started: {{ $userProgress->started_at->format('M d, Y g:i A') }}
                                        @if($userProgress->completed_at)
                                            <br>Completed: {{ $userProgress->completed_at->format('M d, Y g:i A') }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Start listening to track your progress</p>
                        @endif
                    </div>
                </div>
            @endauth

            <!-- Learning Tips -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Learning Tips</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Listen to the audio multiple times before attempting exercises
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Use the loop feature to repeat difficult sections
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Adjust playback speed to match your comprehension level
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Check the transcript only after trying to understand without it
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('audioPlayer');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const rewindBtn = document.getElementById('rewindBtn');
    const forwardBtn = document.getElementById('forwardBtn');
    const speedControl = document.getElementById('speedControl');
    const currentTimeSpan = document.getElementById('currentTime');
    const progressBar = document.getElementById('progressBar');
    const loopCheckbox = document.getElementById('loopCheckbox');
    const loopStartInput = document.getElementById('loopStart');
    const loopEndInput = document.getElementById('loopEnd');
    const transcriptBtn = document.getElementById('transcriptBtn');
    const transcriptSection = document.getElementById('transcriptSection');

    let isLooping = false;
    let loopStart = 0;
    let loopEnd = audio.duration || 60;

    // Play/Pause functionality
    playPauseBtn.addEventListener('click', function() {
        if (audio.paused) {
            audio.play();
            playPauseBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        } else {
            audio.pause();
            playPauseBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path></svg>';
        }
    });

    // Rewind/Forward controls
    rewindBtn.addEventListener('click', () => {
        audio.currentTime = Math.max(0, audio.currentTime - 15);
    });

    forwardBtn.addEventListener('click', () => {
        audio.currentTime = Math.min(audio.duration, audio.currentTime + 15);
    });

    // Speed control
    speedControl.addEventListener('change', function() {
        audio.playbackRate = parseFloat(this.value);
    });

    // Time updates
    audio.addEventListener('timeupdate', function() {
        const current = audio.currentTime;
        const duration = audio.duration;

        if (duration) {
            // Update progress bar
            const progress = (current / duration) * 100;
            progressBar.style.width = progress + '%';

            // Update current time display
            const minutes = Math.floor(current / 60);
            const seconds = Math.floor(current % 60);
            currentTimeSpan.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            // Handle looping
            if (isLooping && current >= loopEnd) {
                audio.currentTime = loopStart;
            }

            // Save progress to server
            @auth
            if (Math.floor(current) % 5 === 0) { // Save every 5 seconds
                saveProgress(Math.floor(current));
            }
            @endauth
        }
    });

    // Loop functionality
    loopCheckbox.addEventListener('change', function() {
        isLooping = this.checked;
        loopStart = parseFloat(loopStartInput.value) || 0;
        loopEnd = parseFloat(loopEndInput.value) || audio.duration;
    });

    loopStartInput.addEventListener('change', function() {
        loopStart = parseFloat(this.value) || 0;
    });

    loopEndInput.addEventListener('change', function() {
        loopEnd = parseFloat(this.value) || audio.duration;
    });

    // Transcript toggle
    if (transcriptBtn && transcriptSection) {
        transcriptBtn.addEventListener('click', function() {
            if (transcriptSection.style.display === 'none') {
                transcriptSection.style.display = 'block';
                this.textContent = 'Hide Transcript';
                this.classList.remove('bg-green-500', 'hover:bg-green-600');
                this.classList.add('bg-red-500', 'hover:bg-red-600');
            } else {
                transcriptSection.style.display = 'none';
                this.textContent = 'Show Transcript';
                this.classList.remove('bg-red-500', 'hover:bg-red-600');
                this.classList.add('bg-green-500', 'hover:bg-green-600');
            }
        });
    }

    // Save progress function
    @auth
    function saveProgress(position) {
        fetch(`{{ route('audio-learning.update-progress', $lesson->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                position: position
            })
        }).catch(console.error);
    }

    // Load user progress
    function loadProgress() {
        fetch(`{{ route('audio-learning.get-progress', $lesson->id) }}`)
            .then(response => response.json())
            .then(data => {
                if (data.total_score !== undefined) {
                    document.getElementById('currentScore').textContent = data.total_score;
                    document.getElementById('maxScore').textContent = data.max_score;
                    document.getElementById('progressPercentage').textContent = data.progress_percentage + '%';
                }
            })
            .catch(console.error);
    }

    // Load progress on page load
    loadProgress();
    @endauth

    // Set duration when metadata loads
    audio.addEventListener('loadedmetadata', function() {
        loopEndInput.value = Math.floor(audio.duration);
        loopEnd = audio.duration;
    });
});
</script>
@endpush
@endsection