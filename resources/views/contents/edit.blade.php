<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $content->exists ? 'Edit Konten: ' . $content->title : 'Buat Konten Baru' }}
            </h2>
            <a href="{{ route('courses.show', $lesson->course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                &larr; Kembali ke Kursus
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" 
                     x-data="contentFormManager({
                         content: {{ Js::from($content) }},
                         createUrl: '{{ route('lessons.contents.store', $lesson) }}',
                         updateUrl: '{{ $content->exists ? route('lessons.contents.update', [$lesson, $content]) : '' }}'
                     })"
                     x-init="initForm()">
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="contentForm" :action="formAction" method="POST" enctype="multipart/form-data">
                        @csrf
                        <template x-if="content.id"><input type="hidden" name="_method" value="PUT"></template>

                        {{-- Form Utama --}}
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Judul Konten</label>
                                <input type="text" name="title" id="title" x-model="content.title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                                <textarea name="description" id="description" x-model="content.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipe Konten</label>
                                <select name="type" id="type" x-model="content.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="text">Teks</option>
                                    <option value="video">Video (URL)</option>
                                    <option value="document">Dokumen</option>
                                    <option value="image">Gambar</option>
                                    <option value="quiz">Kuis</option>
                                    <option value="essay">Esai</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Field Dinamis --}}
                        <div class="mt-4">
                            <div x-show="isType('text') || isType('essay')" x-cloak>
                                <label for="body_editor" class="block text-sm font-medium text-gray-700" x-text="isType('essay') ? 'Pertanyaan Esai' : 'Isi Konten'"></label>
                                <textarea name="body_text" id="body_editor">{{ old('body', $content->body) }}</textarea>
                            </div>
                             <div x-show="isType('video')" x-cloak>
                                <label for="video_url" class="block text-sm font-medium text-gray-700">URL Video YouTube</label>
                                <input type="text" name="body_video" x-model="content.body" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="https://www.youtube.com/watch?v=xxxx">
                            </div>
                            <div x-show="isType('document') || isType('image')" x-cloak>
                                <label for="file_upload" class="block text-sm font-medium text-gray-700">Unggah File</label>
                                <template x-if="content.file_path">
                                    <p class="text-xs text-gray-500 mt-1">File saat ini: <a :href="`/storage/${content.file_path}`" target="_blank" class="text-blue-500" x-text="content.file_path.split('/').pop()"></a></p>
                                </template>
                                <input type="file" name="file_upload" id="file_upload" class="mt-1 block w-full">
                            </div>
                            <div x-show="isType('quiz')" x-cloak>
                                @include('quizzes.partials.full-quiz-form')
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('courses.show', $lesson->course) }}" class="text-sm text-gray-600 mr-4">Batal</a>
                            <button 
                                type="button" 
                                @click="submitForm()" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <span x-text="content.id ? 'Perbarui Konten' : 'Simpan Konten'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.tiny.cloud/1/wfo9boig39silkud2152anvh7iaqnu9wf4wqh75iudy3mry6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        function contentFormManager(data) {
            return {
                content: data.content,
                formAction: data.content.id ? data.updateUrl : data.createUrl,
                
                initForm() {
                    this.$watch('content.type', (newType) => this.handleTypeChange(newType));

                    // ✅ PERBAIKAN UTAMA ADA DI SINI
                    if (this.content.quiz) {
                        // 1. Konversi nilai "show_answers_after_attempt" ke boolean
                        this.content.quiz.show_answers_after_attempt = !!parseInt(this.content.quiz.show_answers_after_attempt);

                        if (this.content.quiz.questions) {
                            this.content.quiz.questions.forEach(q => {
                                // 2. Konversi nilai "is_correct" untuk setiap opsi ke boolean
                                if (q.options) {
                                    q.options.forEach(opt => {
                                        opt.is_correct = !!parseInt(opt.is_correct);
                                    });
                                }
                                // Siapkan data untuk radio button Benar/Salah
                                if (q.type === 'true_false') {
                                    const correctOption = q.options.find(opt => opt.is_correct);
                                    q.correct_answer_tf = correctOption ? correctOption.option_text.toLowerCase() : 'false';
                                }
                            });
                        }
                    } else {
                        // Jika membuat kuis baru, inisialisasi objek kuis
                        this.content.quiz = this.defaultQuizObject();
                    }

                    if (this.content.quiz.questions.length === 0) {
                        this.addQuestion();
                    }

                    this.handleTypeChange(this.content.type);
                },
                
                initTinymce() {
                    if (tinymce.get('body_editor')) tinymce.get('body_editor').destroy();
                    const textarea = document.getElementById('body_editor');
                    if (!textarea) return;
                    const initialContent = textarea.value;
                    tinymce.init({
                        selector: 'textarea#body_editor', height: 300,
                        plugins: 'code table lists link image media autosave wordcount fullscreen template',
                        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link image media',
                        setup: (editor) => {
                            editor.on('init', () => editor.setContent(initialContent));
                        }
                    });
                },

                isType(type) { return this.content.type === type; },
                handleTypeChange(type) {
                    if (type === 'text' || type === 'essay') {
                        this.$nextTick(() => { this.initTinymce(); });
                    } else {
                        if (tinymce.get('body_editor')) tinymce.get('body_editor').destroy();
                    }
                },
                
                submitForm() {
                    if (tinymce.get('body_editor')) tinymce.triggerSave();
                    document.getElementById('contentForm').submit();
                },
                
                defaultQuizObject() { return { title: this.content.title, description: '', total_marks: 100, pass_marks: 70, status: 'draft', show_answers_after_attempt: false, questions: [] }; },
                addQuestion() {
                    this.content.quiz.questions.push({
                        id: null, question_text: '', type: 'multiple_choice', marks: 10, open: true,
                        options: [{ id: null, option_text: '', is_correct: false }],
                        correct_answer_tf: 'false',
                    });
                },
                defaultOptionObject() { return { id: null, option_text: '', is_correct: false }; },
                removeQuestion(qIndex) { if (this.content.quiz.questions.length > 1) this.content.quiz.questions.splice(qIndex, 1); },
                addOption(qIndex) { this.content.quiz.questions[qIndex].options.push(this.defaultOptionObject()); },
                removeOption(qIndex, oIndex) { this.content.quiz.questions[qIndex].options.splice(oIndex, 1); }
            }
        }
    </script>
    @endpush
</x-app-layout>