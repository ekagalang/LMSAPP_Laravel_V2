<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('courses.show', $lesson->course) }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('Kembali ke Detail Kursus') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-2">
                    {{ __('Edit Konten:') }} {{ $content->title }}
                </h2>
                <p class="text-sm text-gray-600">Pelajaran: {{ $lesson->title }}</p>
                <p class="text-sm text-gray-600">Kursus: {{ $lesson->course->title }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="contentForm" method="POST" action="{{ route('lessons.contents.update', [$lesson, $content]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Konten</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('title', $content->title) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Konten</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="text" @selected(old('type', $content->type) == 'text')>Teks</option>
                                <option value="video" @selected(old('type', $content->type) == 'video')>Video (URL)</option>
                                <option value="document" @selected(old('type', $content->type) == 'document')>Dokumen</option>
                                <option value="image" @selected(old('type', $content->type) == 'image')>Gambar</option>
                                <option value="quiz" @selected(old('type', $content->type) == 'quiz')>Kuis</option>
                                <option value="essay" @selected(old('type', $content->type) == 'essay')>Esai</option>
                            </select>
                        </div>

                        <div id="body_field" class="mb-4 hidden">
                            <label for="body" class="block text-sm font-medium text-gray-700">Isi Konten / URL / Pertanyaan</label>
                            <textarea name="body" id="body" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('body', $content->body) }}</textarea>
                        </div>

                        <div id="file_upload_field" class="mb-4 hidden">
                            <label for="file_upload" class="block text-sm font-medium text-gray-700">Unggah File Baru (Opsional)</label>
                             @if($content->file_path)
                                <p class="text-xs text-gray-500 mt-1">File saat ini: <a href="{{ asset('storage/' . $content->file_path) }}" target="_blank" class="text-blue-500">{{ basename($content->file_path) }}</a></p>
                            @endif
                            <input type="file" name="file_upload" id="file_upload" class="mt-1 block w-full">
                        </div>
                        
                        <div id="quiz_form_fields" class="mb-4 hidden"></div>

                        <div class="mb-4">
                            <label for="order" class="block text-sm font-medium text-gray-700">Urutan (Opsional)</label>
                            <input type="number" name="order" id="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('order', $content->order) }}">
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                {{ __('Perbarui Konten') }}
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
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            let questionCounter = 0;
            let alpineScope = null;

            function init() {
                typeSelect.addEventListener('change', handleContentTypeChange);
                handleContentTypeChange();

                document.body.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-question')) {
                        const questionBlock = e.target.closest('.question-block');
                        const questionId = questionBlock.querySelector('input[name$="[id]"]').value;
                        const quizFormRoot = document.getElementById('quiz_form_fields').firstElementChild;
                        const alpineScope = Alpine.$data(quizFormRoot);
                        
                        // Tambahkan ID ke daftar hapus jika ada
                        if (questionId) {
                            const deleteInput = document.createElement('input');
                            deleteInput.type = 'hidden';
                            deleteInput.name = 'questions_to_delete[]';
                            deleteInput.value = questionId;
                            contentForm.appendChild(deleteInput);
                        }
                        
                        const removedIndex = Array.from(questionBlock.parentNode.children).indexOf(questionBlock);
                        questionBlock.remove();

                        // Panggil fungsi di Alpine scope untuk mengupdate state
                        if (alpineScope && typeof alpineScope.removeQuestionTab === 'function') {
                            alpineScope.removeQuestionTab(removedIndex);
                        }
                    }
                });

                document.body.addEventListener('change', function(e) {
                    if (e.target && e.target.matches('select[name*="[type]"]')) {
                        toggleQuestionTypeFields(e.target);
                    }
                });
            }

            function handleContentTypeChange() {
                const type = typeSelect.value;
                const bodyField = document.getElementById('body_field');
                const fileUploadField = document.getElementById('file_upload_field');
                const quizFormContainer = document.getElementById('quiz_form_fields');

                if (tinymce.get('body')) tinymce.get('body').destroy();

                bodyField.style.display = 'none';
                fileUploadField.style.display = 'none';
                quizFormContainer.style.display = 'none';

                if (type === 'text' || type === 'video' || type === 'essay') {
                    bodyField.style.display = 'block';
                    if (type === 'text' || type === 'essay') {
                        tinymce.init({ selector: 'textarea#body' });
                    }
                } else if (type === 'document' || type === 'image') {
                    fileUploadField.style.display = 'block';
                } else if (type === 'quiz') {
                    quizFormContainer.style.display = 'block';
                    loadQuizFormPartial({!! Js::from($content->quiz ? $content->quiz->load('questions.options') : null) !!});
                }
            }

            function loadQuizFormPartial(quizData) {
                const container = document.getElementById('quiz_form_fields');
                // Reset kontainer
                container.innerHTML = '';
                
                fetch("{{ route('quiz-full-form-partial') }}")
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        const alpineEl = container.querySelector('[x-data]');
                        
                        // Set data kuis ke komponen Alpine
                        const alpineComponent = Alpine.$data(alpineEl);
                        if (alpineComponent) {
                            alpineComponent.initializeQuiz(quizData);
                        }
                    });
            }

            function populateQuizForm(data) {
                document.getElementById('quiz_title').value = data.title || '';
                document.getElementById('quiz_description').value = data.description || '';
                document.getElementById('total_marks').value = data.total_marks || 0;
                document.getElementById('pass_marks').value = data.pass_marks || 0;
                document.getElementById('time_limit').value = data.time_limit || '';
                document.getElementById('quiz_status').value = data.status || 'draft';
                document.getElementById('show_answers_after_attempt').checked = data.show_answers_after_attempt || false;
                
                const questionsContainer = document.getElementById('questions-container-for-quiz-form');
                questionsContainer.innerHTML = '';
                questionCounter = 0;

                if (data.questions && data.questions.length > 0) {
                    data.questions.forEach(q => addQuestionToForm(q));
                } else {
                    addQuestionToForm();
                }
            }
            
            function addQuestionToForm(questionData = null) {
                const index = questionCounter++;
                fetch(`{{ route('quiz-question-partial') }}?index=${index}`)
                    .then(response => response.text())
                    .then(html => {
                        const container = document.getElementById('questions-container-for-quiz-form');
                        const questionNode = document.createElement('div');
                        questionNode.innerHTML = html;
                        const questionBlock = questionNode.firstElementChild;
                        questionBlock.dataset.questionIndex = index;
                        
                        if (questionData) {
                            populateQuestionFields(questionBlock, questionData);
                        }
                        
                        container.appendChild(questionBlock);
                        toggleQuestionTypeFields(questionBlock.querySelector('select[name*="[type]"]'));

                        if(alpineScope) alpineScope.addQuestionTab(index);
                    });
            }

            function populateQuestionFields(block, data) {
                block.querySelector('input[name*="[id]"]').value = data.id || '';
                block.querySelector('[name*="[question_text]"]').value = data.question_text || '';
                block.querySelector('[name*="[marks]"]').value = data.marks || 1;
                const typeSelect = block.querySelector('[name*="[type]"]');
                typeSelect.value = data.type || 'multiple_choice';

                if (data.type === 'multiple_choice' && data.options) {
                    const optionsList = block.querySelector('.options-list');
                    optionsList.innerHTML = '';
                    data.options.forEach(opt => addOptionToQuestion(block, opt));
                } else if (data.type === 'true_false' && data.options) {
                    const correctOption = data.options.find(opt => opt.is_correct);
                    if (correctOption) {
                        block.querySelector(`input[name*="[correct_answer_tf]"][value="${correctOption.option_text.toLowerCase()}"]`).checked = true;
                    }
                }
            }

            function addOptionToQuestion(questionBlock, optionData = null) {
                const optionsList = questionBlock.querySelector('.options-list');
                const qIndex = questionBlock.dataset.questionIndex;
                const oIndex = optionsList.children.length;
                const optionHTML = `
                    <div class="option-group flex items-center space-x-2 mb-2">
                        <input type="hidden" name="questions[${qIndex}][options][${oIndex}][id]" value="${optionData ? optionData.id : ''}">
                        <input type="text" name="questions[${qIndex}][options][${oIndex}][option_text]" class="flex-grow rounded-md" value="${optionData ? optionData.option_text : ''}" required>
                        <input type="checkbox" name="questions[${qIndex}][options][${oIndex}][is_correct]" value="1" class="rounded" ${optionData && optionData.is_correct ? 'checked' : ''}>
                        <label class="text-sm">Benar</label>
                        <button type="button" class="remove-option text-red-600">Hapus</button>
                    </div>`;
                optionsList.insertAdjacentHTML('beforeend', optionHTML);
            }

            function toggleQuestionTypeFields(select) {
                const questionBlock = select.closest('.question-block');
                const optionsContainer = questionBlock.querySelector('.options-container');
                const trueFalseOptions = questionBlock.querySelector('.true-false-options');
                
                optionsContainer.style.display = select.value === 'multiple_choice' ? 'block' : 'none';
                trueFalseOptions.style.display = select.value === 'true_false' ? 'block' : 'none';
            }

            function attachQuizFormListeners(container) {
                const addQuestionButton = container.querySelector('#add-question-to-quiz-form');
                if (addQuestionButton) {
                    addQuestionButton.addEventListener('click', function() {
                        // Dapatkan kembali quizFormAlpineScope di sini untuk memastikan ia yang terbaru
                        quizFormAlpineScope = Alpine.$data(quizFormFieldsContainer.firstElementChild); 
                        addQuestionToQuizForm(globalQuestionCounter++);
                    });
                }
            }

            init();
        });
    </script>
    @endpush
</x-app-layout>