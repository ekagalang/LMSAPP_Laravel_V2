<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Video Interaction - {{ $content->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.video-interactions.update', [$content, $videoInteraction]) }}" method="POST" x-data="interactionForm()">
                        @csrf
                        @method('PUT')
                        
                        <!-- Video Preview -->
                        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📹 Video Preview</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Video Player -->
                                <div class="lg:col-span-2">
                                    <div class="aspect-video rounded-lg overflow-hidden shadow-lg bg-black">
                                        <div id="preview-video" class="w-full h-full"></div>
                                    </div>
                                    
                                    <!-- Video Controls -->
                                    <div class="mt-4 bg-white rounded-lg p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-gray-600">
                                                <span class="font-medium">Current Time:</span> 
                                                <span id="current-time">0:00</span>
                                            </div>
                                            <button type="button" id="use-current-time" 
                                                    class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                                Use Current Time
                                            </button>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500">
                                            💡 Klik "Use Current Time" untuk menggunakan waktu saat ini sebagai timestamp interaction
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Video Info -->
                                <div class="space-y-4">
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h4 class="font-medium text-gray-900 mb-2">Video Information</h4>
                                        <div class="space-y-2 text-sm">
                                            <div><span class="text-gray-600">Title:</span> {{ $content->title }}</div>
                                            <div><span class="text-gray-600">Content ID:</span> {{ $content->id }}</div>
                                            <div><span class="text-gray-600">Existing Interactions:</span> {{ $content->videoInteractions()->count() }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h4 class="font-medium text-gray-900 mb-2">Quick Timestamps</h4>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <button type="button" class="timestamp-btn p-2 border rounded hover:bg-gray-50" data-time="15">0:15</button>
                                            <button type="button" class="timestamp-btn p-2 border rounded hover:bg-gray-50" data-time="30">0:30</button>
                                            <button type="button" class="timestamp-btn p-2 border rounded hover:bg-gray-50" data-time="60">1:00</button>
                                            <button type="button" class="timestamp-btn p-2 border rounded hover:bg-gray-50" data-time="120">2:00</button>
                                            <button type="button" class="timestamp-btn p-2 border rounded hover:bg-gray-50" data-time="180">3:00</button>
                                            <button type="button" class="timestamp-btn p-2 border rounded hover:bg-gray-50" data-time="300">5:00</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Current Interaction Info -->
                                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                        <h4 class="font-medium text-yellow-900 mb-2">Editing Interaction</h4>
                                        <div class="space-y-2 text-sm text-yellow-800">
                                            <div><span class="font-medium">Type:</span> {{ ucfirst($videoInteraction->type) }}</div>
                                            <div><span class="font-medium">Current Time:</span> {{ $videoInteraction->timestamp }}s</div>
                                            <div><span class="font-medium">Title:</span> {{ $videoInteraction->title }}</div>
                                        </div>
                                        <button type="button" id="jump-to-interaction" 
                                                class="mt-2 px-2 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                                            Jump to Interaction Time
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Interaksi</label>
                                <select name="type" id="type" x-model="type" required 
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Tipe</option>
                                    <option value="quiz" {{ $videoInteraction->type == 'quiz' ? 'selected' : '' }}>🧠 Quiz - Pertanyaan interaktif</option>
                                    <option value="reflection" {{ $videoInteraction->type == 'reflection' ? 'selected' : '' }}>🤔 Reflection - Refleksi pembelajaran</option>
                                    <option value="annotation" {{ $videoInteraction->type == 'annotation' ? 'selected' : '' }}>📝 Annotation - Catatan penting</option>
                                    <option value="hotspot" {{ $videoInteraction->type == 'hotspot' ? 'selected' : '' }}>📍 Hotspot - Area yang bisa diklik</option>
                                    <option value="overlay" {{ $videoInteraction->type == 'overlay' ? 'selected' : '' }}>🎯 Overlay - Informasi overlay</option>
                                    <option value="pause" {{ $videoInteraction->type == 'pause' ? 'selected' : '' }}>⏸️ Pause - Titik jeda</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="timestamp" class="block text-sm font-medium text-gray-700 mb-2">Waktu (detik)</label>
                                <input type="number" name="timestamp" id="timestamp" step="0.1" min="0" required 
                                       value="{{ $videoInteraction->timestamp }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="misal: 30.5">
                                @error('timestamp')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Waktu dalam detik kapan interaksi akan muncul</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                            <input type="text" name="title" id="title" required value="{{ $videoInteraction->title }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="misal: Quick Knowledge Check">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Deskripsi atau pertanyaan...">{{ $videoInteraction->description }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quiz Specific Fields -->
                        <div x-show="type === 'quiz'" class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-900 mb-4">Pengaturan Quiz</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Jawaban</label>
                                    @php
                                        $options = $videoInteraction->data['options'] ?? [['text' => ''], ['text' => ''], ['text' => ''], ['text' => '']];
                                        $correctAnswer = $videoInteraction->data['correct_answer'] ?? 0;
                                    @endphp
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="correct_answer" value="0" 
                                                   {{ $correctAnswer == 0 ? 'checked' : '' }}
                                                   class="text-blue-600 focus:ring-blue-500">
                                            <input type="text" name="options[0][text]" value="{{ $options[0]['text'] ?? '' }}"
                                                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="Pilihan 1">
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="correct_answer" value="1" 
                                                   {{ $correctAnswer == 1 ? 'checked' : '' }}
                                                   class="text-blue-600 focus:ring-blue-500">
                                            <input type="text" name="options[1][text]" value="{{ $options[1]['text'] ?? '' }}"
                                                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="Pilihan 2">
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="correct_answer" value="2" 
                                                   {{ $correctAnswer == 2 ? 'checked' : '' }}
                                                   class="text-blue-600 focus:ring-blue-500">
                                            <input type="text" name="options[2][text]" value="{{ $options[2]['text'] ?? '' }}"
                                                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="Pilihan 3">
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="correct_answer" value="3" 
                                                   {{ $correctAnswer == 3 ? 'checked' : '' }}
                                                   class="text-blue-600 focus:ring-blue-500">
                                            <input type="text" name="options[3][text]" value="{{ $options[3]['text'] ?? '' }}"
                                                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="Pilihan 4">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Pilih radio button untuk jawaban yang benar</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="correct_feedback" class="block text-sm font-medium text-gray-700 mb-2">Feedback Benar</label>
                                        <input type="text" name="correct_feedback" id="correct_feedback" 
                                               value="{{ $videoInteraction->data['correct_feedback'] ?? '' }}"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="Correct! Well done.">
                                    </div>
                                    <div>
                                        <label for="incorrect_feedback" class="block text-sm font-medium text-gray-700 mb-2">Feedback Salah</label>
                                        <input type="text" name="incorrect_feedback" id="incorrect_feedback" 
                                               value="{{ $videoInteraction->data['incorrect_feedback'] ?? '' }}"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="Incorrect. Try again.">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reflection Specific Fields -->
                        <div x-show="type === 'reflection'" class="mb-6 p-4 bg-purple-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-purple-900 mb-4">Pengaturan Reflection</h3>
                            
                            @php
                                $reflectionData = $videoInteraction->data ?? [];
                                $reflectionType = $reflectionData['reflection_type'] ?? 'text';
                                $reflectionQuestion = $reflectionData['reflection_question'] ?? '';
                                $reflectionOptions = $reflectionData['reflection_options'] ?? [['text' => ''], ['text' => ''], ['text' => ''], ['text' => '']];
                                $reflectionCorrectAnswer = $reflectionData['reflection_correct_answer'] ?? 0;
                                $reflectionIsRequired = $reflectionData['reflection_is_required'] ?? false;
                                $reflectionHasScoring = $reflectionData['reflection_has_scoring'] ?? false;
                                $reflectionCorrectFeedback = $reflectionData['reflection_correct_feedback'] ?? '';
                                $reflectionGeneralFeedback = $reflectionData['reflection_general_feedback'] ?? '';
                            @endphp
                            
                            <div class="space-y-4">
                                <!-- Reflection Type Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Reflection</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="reflection_type" value="text" 
                                                   {{ $reflectionType == 'text' ? 'checked' : '' }}
                                                   class="text-purple-600 focus:ring-purple-500"
                                                   x-model="reflectionType">
                                            <span class="ml-2 text-sm">📝 Open-ended (Text Area)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="reflection_type" value="multiple_choice"
                                                   {{ $reflectionType == 'multiple_choice' ? 'checked' : '' }}
                                                   class="text-purple-600 focus:ring-purple-500"
                                                   x-model="reflectionType">
                                            <span class="ml-2 text-sm">📋 Multiple Choice</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Reflection Question -->
                                <div>
                                    <label for="reflection_question" class="block text-sm font-medium text-gray-700 mb-2">Pertanyaan Reflection</label>
                                    <textarea name="reflection_question" id="reflection_question" rows="3" 
                                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                              placeholder="Contoh: Bagaimana Anda akan menerapkan konsep ini di pekerjaan Anda?">{{ $reflectionQuestion }}</textarea>
                                </div>

                                <!-- Multiple Choice Options (shown only when multiple_choice is selected) -->
                                <div x-show="reflectionType === 'multiple_choice'" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Jawaban</label>
                                        <div class="space-y-2">
                                            <div class="flex items-center space-x-2">
                                                <input type="radio" name="reflection_correct_answer" value="0" 
                                                       {{ $reflectionCorrectAnswer == 0 ? 'checked' : '' }}
                                                       class="text-purple-600 focus:ring-purple-500">
                                                <input type="text" name="reflection_options[0][text]" value="{{ $reflectionOptions[0]['text'] ?? '' }}"
                                                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                                       placeholder="Pilihan 1">
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <input type="radio" name="reflection_correct_answer" value="1" 
                                                       {{ $reflectionCorrectAnswer == 1 ? 'checked' : '' }}
                                                       class="text-purple-600 focus:ring-purple-500">
                                                <input type="text" name="reflection_options[1][text]" value="{{ $reflectionOptions[1]['text'] ?? '' }}"
                                                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                                       placeholder="Pilihan 2">
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <input type="radio" name="reflection_correct_answer" value="2" 
                                                       {{ $reflectionCorrectAnswer == 2 ? 'checked' : '' }}
                                                       class="text-purple-600 focus:ring-purple-500">
                                                <input type="text" name="reflection_options[2][text]" value="{{ $reflectionOptions[2]['text'] ?? '' }}"
                                                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                                       placeholder="Pilihan 3">
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <input type="radio" name="reflection_correct_answer" value="3" 
                                                       {{ $reflectionCorrectAnswer == 3 ? 'checked' : '' }}
                                                       class="text-purple-600 focus:ring-purple-500">
                                                <input type="text" name="reflection_options[3][text]" value="{{ $reflectionOptions[3]['text'] ?? '' }}"
                                                       class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                                       placeholder="Pilihan 4">
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Pilih radio button untuk jawaban yang paling tepat (opsional untuk reflection)</p>
                                    </div>
                                </div>

                                <!-- Reflection Settings -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="reflection_is_required" {{ $reflectionIsRequired ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">Wajib dijawab</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="reflection_has_scoring" {{ $reflectionHasScoring ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">Ada penilaian</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Feedback (for multiple choice) -->
                                <div x-show="reflectionType === 'multiple_choice'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="reflection_correct_feedback" class="block text-sm font-medium text-gray-700 mb-2">Feedback Tepat</label>
                                        <input type="text" name="reflection_correct_feedback" id="reflection_correct_feedback" 
                                               value="{{ $reflectionCorrectFeedback }}"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                               placeholder="Great reflection!">
                                    </div>
                                    <div>
                                        <label for="reflection_general_feedback" class="block text-sm font-medium text-gray-700 mb-2">Feedback Umum</label>
                                        <input type="text" name="reflection_general_feedback" id="reflection_general_feedback" 
                                               value="{{ $reflectionGeneralFeedback }}"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                               placeholder="Terima kasih atas refleksi Anda">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotspot Position -->
                        <div x-show="type === 'hotspot'" class="mb-6 p-4 bg-green-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-900 mb-4">Posisi Hotspot</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="position_x" class="block text-sm font-medium text-gray-700 mb-2">Posisi X (%)</label>
                                    <input type="number" name="position[x]" id="position_x" min="0" max="100" 
                                           value="{{ $videoInteraction->data['position']['x'] ?? '' }}"
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="50">
                                </div>
                                <div>
                                    <label for="position_y" class="block text-sm font-medium text-gray-700 mb-2">Posisi Y (%)</label>
                                    <input type="number" name="position[y]" id="position_y" min="0" max="100" 
                                           value="{{ $videoInteraction->data['position']['y'] ?? '' }}"
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="25">
                                </div>
                            </div>
                        </div>

                        <!-- Overlay Content -->
                        <div x-show="type === 'overlay'" class="mb-6 p-4 bg-purple-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-purple-900 mb-4">Pengaturan Overlay</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="overlay_content" class="block text-sm font-medium text-gray-700 mb-2">Konten Overlay</label>
                                    <textarea name="overlay_content" id="overlay_content" rows="3" 
                                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Konten yang akan ditampilkan di overlay...">{{ $videoInteraction->data['overlay_content'] ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Durasi (detik)</label>
                                    <input type="number" name="duration" id="duration" min="1" max="60" 
                                           value="{{ $videoInteraction->data['duration'] ?? 5 }}"
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="5">
                                    <p class="text-xs text-gray-500 mt-1">Berapa lama overlay akan ditampilkan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Annotation Content -->
                        <div x-show="type === 'annotation'" class="mb-6 p-4 bg-yellow-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-900 mb-4">Pengaturan Annotation</h3>
                            <div>
                                <label for="annotation_text" class="block text-sm font-medium text-gray-700 mb-2">Teks Annotation</label>
                                <textarea name="annotation_text" id="annotation_text" rows="3" 
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Catatan atau penjelasan tambahan...">{{ $videoInteraction->data['annotation_text'] ?? '' }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Teks yang akan ditampilkan sebagai catatan</p>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="mb-6 flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" {{ $videoInteraction->is_active ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                            </label>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.video-interactions.index', $content) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Interaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let player;
        let currentTime = 0;
        let isPlayerReady = false;
        const currentInteractionTime = {{ $videoInteraction->timestamp }};

        function interactionForm() {
            return {
                type: '{{ $videoInteraction->type }}',
                reflectionType: '{{ $reflectionType }}'
            }
        }

        // YouTube API Ready callback
        function onYouTubeIframeAPIReady() {
            const videoId = "{{ $content->youtube_video_id }}";
            
            console.log('Video ID:', videoId);
            console.log('Content body:', "{{ addslashes($content->body ?? '') }}");
            
            if (videoId && videoId !== '') {
                player = new YT.Player('preview-video', {
                    videoId: videoId,
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });
            } else {
                // Show error message
                document.getElementById('preview-video').innerHTML = 
                    '<div class="flex items-center justify-center h-full text-white bg-gray-800"><div class="text-center"><p class="mb-2">❌ Video tidak ditemukan</p><p class="text-sm">Pastikan content body berisi URL YouTube yang valid</p></div></div>';
            }
        }

        function onPlayerReady(event) {
            isPlayerReady = true;
            console.log('YouTube player is ready');
            
            // Start time update interval
            setInterval(updateCurrentTime, 1000);
        }

        function onPlayerStateChange(event) {
            // Handle player state changes if needed
        }

        function extractVideoId(url) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        }

        function updateCurrentTime() {
            if (isPlayerReady && player && player.getCurrentTime) {
                try {
                    currentTime = player.getCurrentTime();
                    const minutes = Math.floor(currentTime / 60);
                    const seconds = Math.floor(currentTime % 60);
                    const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    
                    const timeDisplay = document.getElementById('current-time');
                    if (timeDisplay) {
                        timeDisplay.textContent = timeString;
                    }
                } catch (error) {
                    console.log('Error getting current time:', error);
                }
            }
        }

        function useCurrentTime() {
            if (currentTime > 0) {
                const timestampInput = document.getElementById('timestamp');
                if (timestampInput) {
                    timestampInput.value = Math.round(currentTime * 10) / 10; // Round to 1 decimal place
                    timestampInput.focus();
                    
                    // Show success feedback
                    showTimestampFeedback('Current time added: ' + timestampInput.value + 's');
                }
            } else {
                showTimestampFeedback('Please play the video first', 'error');
            }
        }

        function setTimestamp(seconds) {
            if (isPlayerReady && player && player.seekTo) {
                try {
                    player.seekTo(seconds, true);
                    
                    // Also set the input field
                    const timestampInput = document.getElementById('timestamp');
                    if (timestampInput) {
                        timestampInput.value = seconds;
                    }
                    
                    showTimestampFeedback(`Jumped to ${Math.floor(seconds/60)}:${(seconds%60).toString().padStart(2, '0')}`);
                } catch (error) {
                    console.log('Error seeking to time:', error);
                    showTimestampFeedback('Error jumping to time', 'error');
                }
            } else {
                showTimestampFeedback('Video player not ready', 'error');
            }
        }

        function jumpToInteractionTime() {
            setTimestamp(currentInteractionTime);
        }

        function showTimestampFeedback(message, type = 'success') {
            // Remove existing feedback
            const existingFeedback = document.querySelector('.timestamp-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            // Create feedback element
            const feedback = document.createElement('div');
            feedback.className = `timestamp-feedback fixed top-4 right-4 px-4 py-2 rounded-lg text-sm font-medium z-50 ${
                type === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white'
            }`;
            feedback.textContent = message;
            
            document.body.appendChild(feedback);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                feedback.remove();
            }, 3000);
        }

        // Load YouTube API
        function loadYouTubeAPI() {
            if (!window.YT) {
                const tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                const firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            } else {
                onYouTubeIframeAPIReady();
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Load YouTube API
            loadYouTubeAPI();
            
            // Add event listeners for timestamp buttons
            document.querySelectorAll('.timestamp-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const time = parseInt(this.dataset.time);
                    setTimestamp(time);
                });
            });
            
            // Add event listener for "Use Current Time" button
            document.getElementById('use-current-time').addEventListener('click', useCurrentTime);
            
            // Add event listener for "Jump to Interaction" button
            document.getElementById('jump-to-interaction').addEventListener('click', jumpToInteractionTime);
        });
    </script>
</x-app-layout>