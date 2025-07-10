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
                         lessonId: {{ $lesson->id }},
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
                                <textarea name="body" x-model="content.body" class="hidden"></textarea>
                                <div id="body_editor"></div>
                            </div>

                             <div x-show="isType('video')" x-cloak>
                                <label for="video_url" class="block text-sm font-medium text-gray-700">URL Video YouTube</label>
                                <input type="text" id="video_url" name="body" x-model="content.body" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="https://www.youtube.com/watch?v=xxxx">
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
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
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
                    this.initTinymce();

                    if (this.isType('quiz') && !this.content.quiz) {
                        this.content.quiz = this.defaultQuizObject();
                    }
                    if (this.content.quiz) {
                        this.content.quiz.questions = this.content.quiz.questions || [];
                        if (this.content.quiz.questions.length === 0) {
                            this.addQuestion();
                        }
                        this.content.quiz.questions.forEach(q => {
                            q.options = q.options || [];
                            q.open = q.open === undefined ? true : q.open;
                        });
                    }
                },

                initTinymce() {
                    tinymce.init({
                        selector: '#body_editor',
                        height: 300,
                        setup: (editor) => {
                            editor.on('init', () => {
                                editor.setContent(this.content.body || '');
                                this.handleTypeChange(this.content.type);
                            });
                            editor.on('change', () => {
                                this.content.body = editor.getContent();
                            });
                            editor.on('keyup', () => {
                                this.content.body = editor.getContent();
                            });
                        }
                    });
                },

                isType(type) { return this.content.type === type; },
                handleTypeChange(type) {
                    const editor = tinymce.get('body_editor');
                    if (!editor) return;
                    if (this.isType('text') || this.isType('essay')) {
                        editor.show();
                    } else {
                        editor.hide();
                    }
                },
                
                defaultQuizObject() { return { title: this.content.title, description: '', total_marks: 100, pass_marks: 70, status: 'draft', show_answers_after_attempt: false, questions: [this.defaultQuestionObject()] }; },
                defaultQuestionObject() { return { id: null, question_text: '', type: 'multiple_choice', marks: 10, options: [this.defaultOptionObject()], open: true }; },
                defaultOptionObject() { return { id: null, option_text: '', is_correct: false }; },
                addQuestion() { this.content.quiz.questions.push(this.defaultQuestionObject()); },
                removeQuestion(qIndex) { if (this.content.quiz.questions.length > 1) this.content.quiz.questions.splice(qIndex, 1); },
                addOption(qIndex) { this.content.quiz.questions[qIndex].options.push(this.defaultOptionObject()); },
                removeOption(qIndex, oIndex) { this.content.quiz.questions[qIndex].options.splice(oIndex, 1); }
            }
        }
    </script>
    @endpush
</x-app-layout>
