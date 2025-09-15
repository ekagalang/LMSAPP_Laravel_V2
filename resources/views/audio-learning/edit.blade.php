@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Microlearning</h1>
                    <p class="text-gray-600">Update interactive microlearning content with audio, video, and exercises</p>
                </div>
                <a href="{{ route('audio-learning.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Microlearning
                </a>
            </div>
        </div>

        <form action="{{ route('audio-learning.update', $audioLesson->id) }}" method="POST" enctype="multipart/form-data"
              x-data="audioLearningForm()" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Basic Information
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="lg:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            📝 Lesson Title *
                        </label>
                        <input type="text" id="title" name="title" required
                               value="{{ old('title', $audioLesson->title) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                               placeholder="Enter lesson title...">
                        @error('title')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            📋 Description
                        </label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                  placeholder="Describe what students will learn...">{{ old('description', $audioLesson->description) }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="difficulty_level" class="block text-sm font-semibold text-gray-700 mb-2">
                            🎯 Difficulty Level *
                        </label>
                        <select id="difficulty_level" name="difficulty_level" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200">
                            <option value="">Select difficulty...</option>
                            <option value="beginner" {{ old('difficulty_level', $audioLesson->difficulty_level) == 'beginner' ? 'selected' : '' }}>🟢 Beginner</option>
                            <option value="intermediate" {{ old('difficulty_level', $audioLesson->difficulty_level) == 'intermediate' ? 'selected' : '' }}>🟡 Intermediate</option>
                            <option value="advanced" {{ old('difficulty_level', $audioLesson->difficulty_level) == 'advanced' ? 'selected' : '' }}>🔴 Advanced</option>
                        </select>
                        @error('difficulty_level')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="available_for_courses" name="available_for_courses" value="1"
                               {{ old('available_for_courses', $audioLesson->available_for_courses) ? 'checked' : '' }}
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="available_for_courses" class="ml-2 text-sm font-medium text-gray-700">
                            📚 Make available for course integration
                        </label>
                        @error('available_for_courses')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Content Type Selection -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2h5a1 1 0 110 2h-1v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6H2a1 1 0 110-2h5z"></path>
                    </svg>
                    Content Type *
                </h3>

                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-data="{ contentType: '{{ old('content_type', $audioLesson->content_type) }}' }">
                        <label class="cursor-pointer">
                            <input type="radio" name="content_type" value="audio" class="sr-only"
                                   x-model="contentType" @change="updateContentType('audio')"
                                   {{ old('content_type', $audioLesson->content_type) == 'audio' ? 'checked' : '' }}>
                            <div class="border-2 border-gray-300 rounded-lg p-4 text-center transition-all hover:border-blue-400"
                                 :class="contentType === 'audio' ? 'border-blue-500 bg-blue-50' : ''">
                                <div class="text-3xl mb-2">🎵</div>
                                <div class="font-semibold text-gray-800">Audio Only</div>
                                <div class="text-sm text-gray-600">MP3, WAV, M4A, AAC</div>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="content_type" value="video" class="sr-only"
                                   x-model="contentType" @change="updateContentType('video')"
                                   {{ old('content_type', $audioLesson->content_type) == 'video' ? 'checked' : '' }}>
                            <div class="border-2 border-gray-300 rounded-lg p-4 text-center transition-all hover:border-blue-400"
                                 :class="contentType === 'video' ? 'border-blue-500 bg-blue-50' : ''">
                                <div class="text-3xl mb-2">🎥</div>
                                <div class="font-semibold text-gray-800">Video Only</div>
                                <div class="text-sm text-gray-600">MP4, MOV, AVI, MKV, WebM</div>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="content_type" value="mixed" class="sr-only"
                                   x-model="contentType" @change="updateContentType('mixed')"
                                   {{ old('content_type', $audioLesson->content_type) == 'mixed' ? 'checked' : '' }}>
                            <div class="border-2 border-gray-300 rounded-lg p-4 text-center transition-all hover:border-blue-400"
                                 :class="contentType === 'mixed' ? 'border-blue-500 bg-blue-50' : ''">
                                <div class="text-3xl mb-2">🎬</div>
                                <div class="font-semibold text-gray-800">Mixed Media</div>
                                <div class="text-sm text-gray-600">Both Audio & Video</div>
                            </div>
                        </label>
                    </div>
                    @error('content_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Media Display -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Current Media Files
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($audioLesson->hasAudio())
                        <div class="bg-teal-50 rounded-lg p-4 border border-teal-200">
                            <div class="flex items-center mb-3">
                                <span class="text-2xl mr-2">🎵</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Current Audio File</h4>
                                    <p class="text-sm text-gray-600">Duration: {{ $audioLesson->formatted_duration }}</p>
                                </div>
                            </div>
                            <audio controls class="w-full">
                                <source src="{{ $audioLesson->audio_url }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    @endif

                    @if($audioLesson->hasVideo())
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                            <div class="flex items-center mb-3">
                                <span class="text-2xl mr-2">🎥</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Current Video File</h4>
                                    <p class="text-sm text-gray-600">Duration: {{ $audioLesson->formatted_duration }}</p>
                                </div>
                            </div>
                            <video controls class="w-full max-h-48 rounded-lg">
                                <source src="{{ $audioLesson->video_url }}" type="video/mp4">
                                Your browser does not support the video element.
                            </video>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Media Upload (Optional - to replace current files) -->
            <div class="bg-white rounded-xl shadow-lg p-8" x-data="{ contentType: '{{ old('content_type', $audioLesson->content_type) }}' }">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                    <span x-text="contentType === 'audio' ? 'Replace Audio File' : contentType === 'video' ? 'Replace Video File' : 'Replace Media Files'"></span>
                    <span class="text-sm font-normal text-gray-500 ml-2">(Optional)</span>
                </h3>

                <div class="space-y-4">
                    <!-- Audio Upload Area -->
                    <div x-show="contentType === 'audio' || contentType === 'mixed'"
                         class="border-2 border-dashed border-teal-300 rounded-xl p-8 text-center hover:border-teal-400 transition-colors duration-300"
                         id="audio_drop_zone"
                         ondragover="handleDragOver(event)"
                         ondragenter="handleDragEnter(event, 'audio')"
                         ondragleave="handleDragLeave(event, 'audio')"
                         ondrop="handleDrop(event, 'audio_file')">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <label for="audio_file" class="relative cursor-pointer bg-white rounded-md font-medium text-teal-600 hover:text-teal-500">
                                <span x-text="contentType === 'mixed' ? 'Replace audio file (optional)' : 'Replace audio file'"></span>
                                <input id="audio_file" name="audio_file" type="file" class="sr-only"
                                       accept="audio/*" onchange="handleMediaSelect('audio', this)">
                            </label>
                            <p class="pl-1">or drag and drop file here</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">MP3, WAV, M4A, AAC up to 50MB</p>
                    </div>

                    <!-- Video Upload Area -->
                    <div x-show="contentType === 'video' || contentType === 'mixed'"
                         class="border-2 border-dashed border-purple-300 rounded-xl p-8 text-center hover:border-purple-400 transition-colors duration-300"
                         id="video_drop_zone"
                         ondragover="handleDragOver(event)"
                         ondragenter="handleDragEnter(event, 'video')"
                         ondragleave="handleDragLeave(event, 'video')"
                         ondrop="handleDrop(event, 'video_file')">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <label for="video_file" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500">
                                <span x-text="contentType === 'mixed' ? 'Replace video file (optional)' : 'Replace video file'"></span>
                                <input id="video_file" name="video_file" type="file" class="sr-only"
                                       accept="video/*" onchange="handleMediaSelect('video', this)">
                            </label>
                            <p class="pl-1">or drag and drop file here</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">MP4, MOV, AVI, MKV, WebM up to 200MB</p>
                    </div>

                    <!-- Audio Preview -->
                    <div id="audio_preview" style="display: none;" class="bg-teal-50 rounded-lg p-4 border border-teal-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">New Audio:</p>
                                    <span id="audio_file_name" class="text-teal-600 text-sm"></span>
                                </div>
                            </div>
                        </div>
                        <audio id="audio_player" controls class="w-full">
                            Your browser does not support the audio element.
                        </audio>
                    </div>

                    <!-- Video Preview -->
                    <div id="video_preview" style="display: none;" class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">New Video:</p>
                                    <span id="video_file_name" class="text-purple-600 text-sm"></span>
                                </div>
                            </div>
                        </div>
                        <video id="video_player" controls class="w-full max-h-64">
                            Your browser does not support the video element.
                        </video>
                    </div>

                    @error('audio_file')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                    @error('video_file')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Transcript -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Transcript (Optional)
                </h3>

                <textarea id="transcript" name="transcript" rows="8"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200"
                          placeholder="Enter the audio transcript to help students learn...">{{ old('transcript', $audioLesson->transcript) }}</textarea>
                <p class="text-sm text-gray-600 mt-2">💡 Providing a transcript helps students learn better and improves accessibility.</p>

                @error('transcript')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('audio-learning.index') }}"
                   class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Microlearning
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Same drag and drop and media handling functions as create form
    function handleDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'copy';
    }

    function handleDragEnter(event, type) {
        event.preventDefault();
        const dropZone = event.currentTarget;

        if (type === 'video') {
            dropZone.classList.add('border-purple-500', 'bg-purple-50');
            dropZone.classList.remove('border-purple-300');
        } else {
            dropZone.classList.add('border-teal-500', 'bg-teal-50');
            dropZone.classList.remove('border-teal-300');
        }
    }

    function handleDragLeave(event, type) {
        event.preventDefault();
        const dropZone = event.currentTarget;

        if (type === 'video') {
            dropZone.classList.remove('border-purple-500', 'bg-purple-50');
            dropZone.classList.add('border-purple-300');
        } else {
            dropZone.classList.remove('border-teal-500', 'bg-teal-50');
            dropZone.classList.add('border-teal-300');
        }
    }

    function handleDrop(event, inputId) {
        event.preventDefault();
        const dropZone = event.currentTarget;
        const files = event.dataTransfer.files;

        // Remove drag visual feedback
        if (inputId === 'video_file') {
            dropZone.classList.remove('border-purple-500', 'bg-purple-50');
            dropZone.classList.add('border-purple-300');
        } else {
            dropZone.classList.remove('border-teal-500', 'bg-teal-50');
            dropZone.classList.add('border-teal-300');
        }

        if (files.length === 0) return;

        const file = files[0];
        const input = document.getElementById(inputId);

        // Validate file type
        const accept = input.getAttribute('accept');
        const isValid = validateFileType(file, accept);

        if (!isValid) {
            alert('File type not supported. Please select a valid media file.');
            return;
        }

        // Create FileList and assign to input
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;

        // Handle file selection based on input type
        if (inputId === 'audio_file') {
            handleMediaSelect('audio', input);
        } else if (inputId === 'video_file') {
            handleMediaSelect('video', input);
        }
    }

    function validateFileType(file, acceptString) {
        if (!acceptString) return true;

        const acceptedTypes = acceptString.split(',').map(type => type.trim().toLowerCase());
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        const fileMimeType = file.type.toLowerCase();

        return acceptedTypes.some(type => {
            if (type.startsWith('.')) {
                return fileExtension === type;
            } else if (type.includes('/*')) {
                const mainType = type.split('/')[0];
                return fileMimeType.startsWith(mainType + '/');
            } else {
                return fileMimeType === type;
            }
        });
    }

    function handleMediaSelect(type, input) {
        const file = input.files[0];
        if (!file) return;

        if (type === 'audio') {
            const preview = document.getElementById('audio_preview');
            const fileName = document.getElementById('audio_file_name');
            const player = document.getElementById('audio_player');

            fileName.textContent = file.name;
            const url = URL.createObjectURL(file);
            player.src = url;
            preview.style.display = 'block';
        } else if (type === 'video') {
            const preview = document.getElementById('video_preview');
            const fileName = document.getElementById('video_file_name');
            const player = document.getElementById('video_player');

            fileName.textContent = file.name;
            const url = URL.createObjectURL(file);
            player.src = url;
            preview.style.display = 'block';
        }
    }

    function updateContentType(type) {
        // This function will be called when content type changes
        // Hide/show appropriate upload sections handled by Alpine.js
        console.log('Content type changed to:', type);
    }

    // Alpine.js component for edit form
    function audioLearningForm() {
        return {
            init() {
                // Set up global reference for non-Alpine event handlers
                window.audioFormInstance = this;
            }
        };
    }
</script>
@endsection