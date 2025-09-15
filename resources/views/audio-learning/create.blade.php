@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Create New Audio Learning</h1>
                    <p class="text-gray-600">Create interactive audio lessons for your students</p>
                </div>
                <a href="{{ route('audio-learning.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Audio Learning
                </a>
            </div>
        </div>

        <form action="{{ route('audio-learning.store') }}" method="POST" enctype="multipart/form-data"
              x-data="audioLearningForm()" class="space-y-8">
            @csrf

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
                               value="{{ old('title') }}"
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
                                  placeholder="Describe what students will learn...">{{ old('description') }}</textarea>
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
                            <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }}>🟢 Beginner</option>
                            <option value="intermediate" {{ old('difficulty_level') == 'intermediate' ? 'selected' : '' }}>🟡 Intermediate</option>
                            <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>🔴 Advanced</option>
                        </select>
                        @error('difficulty_level')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="available_for_courses" name="available_for_courses" value="1"
                               {{ old('available_for_courses') ? 'checked' : '' }}
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

            <!-- Audio Upload -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                    Audio File
                </h3>

                <div class="space-y-4">
                    <!-- Audio Upload Area -->
                    <div class="border-2 border-dashed border-teal-300 rounded-xl p-8 text-center hover:border-teal-400 transition-colors duration-300"
                         id="audio_drop_zone"
                         ondragover="handleDragOver(event)"
                         ondragenter="handleDragEnter(event, 'audio')"
                         ondragleave="handleDragLeave(event, 'audio')"
                         ondrop="handleDrop(event, 'audio_file')">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" />
                            </svg>
                        </div>
                        <div class="text-sm">
                            <label for="audio_file" class="relative cursor-pointer bg-white rounded-md font-medium text-teal-600 hover:text-teal-500">
                                <span>Upload audio file *</span>
                                <input id="audio_file" name="audio_file" type="file" class="sr-only" required
                                       accept="audio/*" onchange="handleAudioSelect(this)">
                            </label>
                            <p class="pl-1">atau drag and drop file di sini</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">MP3, WAV, M4A, AAC up to 50MB</p>
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
                                    <p class="font-medium text-gray-900">Selected Audio:</p>
                                    <span id="audio_file_name" class="text-teal-600 text-sm"></span>
                                </div>
                            </div>
                        </div>
                        <audio id="audio_player" controls class="w-full">
                            Your browser does not support the audio element.
                        </audio>
                    </div>

                    @error('audio_file')
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
                          placeholder="Enter the audio transcript to help students learn...">{{ old('transcript') }}</textarea>
                <p class="text-sm text-gray-600 mt-2">💡 Providing a transcript helps students learn better and improves accessibility.</p>

                @error('transcript')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Exercises (Optional) -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Exercises (Optional)
                </h3>

                <div class="mb-4">
                    <p class="text-gray-600 text-sm">Add interactive exercises to test student comprehension. You can add more exercises later.</p>
                </div>

                <div id="exercises_container" class="space-y-6">
                    <!-- Exercises will be dynamically added here -->
                </div>

                <button type="button" @click="addExercise()"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Exercise
                </button>
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
                        Create Audio Learning
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Drag and Drop Functionality (same as content forms)
    function handleDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'copy';
    }

    function handleDragEnter(event, type) {
        event.preventDefault();
        const dropZone = event.currentTarget;
        dropZone.classList.add('border-teal-500', 'bg-teal-50');
        dropZone.classList.remove('border-teal-300');
    }

    function handleDragLeave(event, type) {
        event.preventDefault();
        const dropZone = event.currentTarget;
        dropZone.classList.remove('border-teal-500', 'bg-teal-50');
        dropZone.classList.add('border-teal-300');
    }

    function handleDrop(event, inputId) {
        event.preventDefault();
        const dropZone = event.currentTarget;
        const files = event.dataTransfer.files;

        // Remove drag visual feedback
        dropZone.classList.remove('border-teal-500', 'bg-teal-50');
        dropZone.classList.add('border-teal-300');

        if (files.length === 0) return;

        const file = files[0];
        const input = document.getElementById(inputId);

        // Validate file type
        const accept = input.getAttribute('accept');
        const isValid = validateFileType(file, accept);

        if (!isValid) {
            alert('Tipe file tidak didukung. Silakan pilih file audio yang valid.');
            return;
        }

        // Create FileList and assign to input
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;

        // Handle file selection
        handleAudioSelect(input);
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

    function handleAudioSelect(input) {
        const file = input.files[0];
        if (!file) return;

        const preview = document.getElementById('audio_preview');
        const fileName = document.getElementById('audio_file_name');
        const player = document.getElementById('audio_player');

        fileName.textContent = file.name;

        // Create URL for audio preview
        const url = URL.createObjectURL(file);
        player.src = url;

        preview.style.display = 'block';
    }

    // Alpine.js component for exercises
    function audioLearningForm() {
        return {
            exercises: [],

            addExercise() {
                const exerciseId = Date.now();
                this.exercises.push({
                    id: exerciseId,
                    question_text: '',
                    type: 'multiple_choice',
                    correct_answers: [''],
                    points: 10,
                    explanation: ''
                });

                this.$nextTick(() => {
                    this.renderExercises();
                });
            },

            removeExercise(index) {
                this.exercises.splice(index, 1);
                this.renderExercises();
            },

            renderExercises() {
                const container = document.getElementById('exercises_container');
                container.innerHTML = '';

                this.exercises.forEach((exercise, index) => {
                    const exerciseHtml = this.generateExerciseHtml(exercise, index);
                    container.innerHTML += exerciseHtml;
                });

                // Add event listeners for dynamic behavior
                this.addExerciseEventListeners();
            },

            generateExerciseHtml(exercise, index) {
                const typeHints = {
                    'multiple_choice': '💡 Create multiple choice options (A, B, C, D). Use "correct_answers" to specify the right option.',
                    'fill_blank': '💡 Create questions with blanks. Example: "The cat is ___". Answer: "sleeping"',
                    'speech_response': '💡 Students will speak their answer. Set keywords they should mention.',
                    'comprehension': '💡 Open-ended questions about audio content. Students write detailed answers.'
                };

                let answersSection = '';
                if (exercise.type === 'multiple_choice') {
                    answersSection = `
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Multiple Choice Options *</label>
                            <div class="space-y-2" id="options_${index}">
                                ${this.generateOptionsHtml(exercise, index)}
                            </div>
                            <button type="button" onclick="Alpine.store('audioForm').addOption(${index})"
                                    class="mt-2 text-sm text-orange-600 hover:text-orange-700">
                                + Add Option
                            </button>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer *</label>
                            <select name="exercises[${index}][correct_answer_index]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500">
                                <option value="">Select correct option...</option>
                                ${exercise.options ? exercise.options.map((opt, i) =>
                                    `<option value="${i}" ${i == exercise.correct_answer_index ? 'selected' : ''}>${String.fromCharCode(65 + i)}. ${opt}</option>`
                                ).join('') : ''}
                            </select>
                        </div>`;
                } else {
                    answersSection = `
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ${exercise.type === 'fill_blank' ? 'Correct Answer(s)' :
                                  exercise.type === 'speech_response' ? 'Expected Keywords' : 'Sample Answer'} *
                            </label>
                            <input type="text" name="exercises[${index}][correct_answers][]" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500"
                                   placeholder="${exercise.type === 'fill_blank' ? 'Enter correct answer...' :
                                               exercise.type === 'speech_response' ? 'keywords, separated, by, commas' :
                                               'Enter sample/expected answer...'}"
                                   value="${exercise.correct_answers[0] || ''}">
                            <p class="text-xs text-gray-500 mt-1">
                                ${exercise.type === 'fill_blank' ? 'For multiple correct answers, separate with commas' :
                                  exercise.type === 'speech_response' ? 'Keywords students should mention in their speech' :
                                  'This helps with grading guidelines'}
                            </p>
                        </div>`;
                }

                return `
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-lg font-medium text-gray-800">Exercise ${index + 1}</h4>
                            <button type="button" onclick="Alpine.store('audioForm').removeExercise(${index})"
                                    class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question *</label>
                                <textarea name="exercises[${index}][question_text]" required rows="2"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500"
                                       placeholder="Enter exercise question...">${exercise.question_text}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                <select name="exercises[${index}][type]" onchange="Alpine.store('audioForm').changeExerciseType(${index}, this.value)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500">
                                    <option value="multiple_choice" ${exercise.type === 'multiple_choice' ? 'selected' : ''}>📋 Multiple Choice</option>
                                    <option value="fill_blank" ${exercise.type === 'fill_blank' ? 'selected' : ''}>✏️ Fill in Blanks</option>
                                    <option value="speech_response" ${exercise.type === 'speech_response' ? 'selected' : ''}>🗣️ Speech Response</option>
                                    <option value="comprehension" ${exercise.type === 'comprehension' ? 'selected' : ''}>🧠 Comprehension</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                                <input type="number" name="exercises[${index}][points]" min="1" max="100"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500"
                                       value="${exercise.points}">
                            </div>

                            <div class="md:col-span-2">
                                <div class="bg-blue-50 p-3 rounded-lg text-sm text-blue-800">
                                    ${typeHints[exercise.type] || ''}
                                </div>
                            </div>

                            ${answersSection}

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                                <textarea name="exercises[${index}][explanation]" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500"
                                          placeholder="Explain the answer or provide additional context...">${exercise.explanation}</textarea>
                            </div>
                        </div>
                    </div>
                `;
            },

            generateOptionsHtml(exercise, exerciseIndex) {
                if (!exercise.options || exercise.options.length === 0) {
                    exercise.options = ['', '', '', ''];
                }

                return exercise.options.map((option, optionIndex) => `
                    <div class="flex items-center space-x-2">
                        <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-medium">
                            ${String.fromCharCode(65 + optionIndex)}
                        </span>
                        <input type="text" name="exercises[${exerciseIndex}][options][]"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:border-orange-500"
                               placeholder="Enter option ${String.fromCharCode(65 + optionIndex)}..."
                               value="${option}"
                               onchange="Alpine.store('audioForm').updateOption(${exerciseIndex}, ${optionIndex}, this.value)">
                        ${optionIndex > 1 ? `
                        <button type="button" onclick="Alpine.store('audioForm').removeOption(${exerciseIndex}, ${optionIndex})"
                                class="text-red-600 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>` : ''}
                    </div>
                `).join('');
            },

            changeExerciseType(exerciseIndex, newType) {
                // Preserve existing data when changing type
                const exercise = this.exercises[exerciseIndex];
                exercise.type = newType;

                // Initialize options for multiple choice
                if (newType === 'multiple_choice' && !exercise.options) {
                    exercise.options = ['', '', '', ''];
                    exercise.correct_answer_index = 0;
                } else if (newType !== 'multiple_choice') {
                    // For non-multiple choice, ensure correct_answers is array
                    if (!Array.isArray(exercise.correct_answers)) {
                        exercise.correct_answers = [exercise.correct_answers || ''];
                    }
                }

                this.renderExercises();
            },

            addOption(exerciseIndex) {
                if (!this.exercises[exerciseIndex].options) {
                    this.exercises[exerciseIndex].options = [];
                }
                this.exercises[exerciseIndex].options.push('');
                this.renderExercises();
            },

            removeOption(exerciseIndex, optionIndex) {
                if (this.exercises[exerciseIndex].options.length > 2) {
                    this.exercises[exerciseIndex].options.splice(optionIndex, 1);
                    this.renderExercises();
                }
            },

            updateOption(exerciseIndex, optionIndex, value) {
                if (this.exercises[exerciseIndex].options) {
                    this.exercises[exerciseIndex].options[optionIndex] = value;
                }
            },

            addExerciseEventListeners() {
                // Add event listeners for form inputs to persist data
                const inputs = document.querySelectorAll('#exercises_container input, #exercises_container textarea, #exercises_container select');
                inputs.forEach(input => {
                    input.addEventListener('change', (e) => this.updateExerciseData(e));
                    input.addEventListener('input', (e) => this.updateExerciseData(e));
                });
            },

            updateExerciseData(event) {
                const target = event.target;
                const name = target.name;

                // Parse the input name to get exercise index and field
                const match = name.match(/exercises\[(\d+)\]\[(.*?)\]/);
                if (match) {
                    const exerciseIndex = parseInt(match[1]);
                    const fieldName = match[2];

                    if (this.exercises[exerciseIndex]) {
                        if (fieldName === 'question_text' || fieldName === 'points' || fieldName === 'explanation') {
                            this.exercises[exerciseIndex][fieldName] = target.value;
                        } else if (fieldName === 'correct_answers[]') {
                            this.exercises[exerciseIndex].correct_answers = [target.value];
                        }
                    }
                }
            },

            init() {
                // Set up Alpine store
                Alpine.store('audioForm', this);
            }
        };
    }
</script>
@endsection