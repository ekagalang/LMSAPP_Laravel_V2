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
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('title', $content->title) }}" required autofocus>
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Konten</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleContentTypeFields()">
                                <option value="text" {{ old('type', $content->type) == 'text' ? 'selected' : '' }}>Teks</option>
                                <option value="video" {{ old('type', $content->type) == 'video' ? 'selected' : '' }}>Video (URL YouTube/Vimeo)</option>
                                <option value="document" {{ old('type', $content->type) == 'document' ? 'selected' : '' }}>Dokumen (PDF, DOCX, PPTX)</option>
                                <option value="image" {{ old('type', $content->type) == 'image' ? 'selected' : '' }}>Gambar (JPG, PNG)</option>
                                <option value="quiz" {{ old('type', $content->type) == 'quiz' ? 'selected' : '' }}>Kuis</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="body_field" class="mb-4 {{ in_array(old('type', $content->type), ['text', 'video']) ? '' : 'hidden' }}">
                            <label for="body" class="block text-sm font-medium text-gray-700">Isi Konten / URL</label>
                            <textarea name="body" id="body" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('body', $content->body) }}</textarea>
                            @error('body')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Untuk Video, masukkan URL YouTube/Vimeo.</p>
                        </div>

                        <div id="file_upload_field" class="mb-4 {{ in_array(old('type', $content->type), ['document', 'image']) ? '' : 'hidden' }}">
                            <label for="file_upload" class="block text-sm font-medium text-gray-700">Unggah File Baru (Kosongkan jika tidak diubah)</label>
                            <input type="file" name="file_upload" id="file_upload" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                            @error('file_upload')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">File saat ini: @if($content->file_path) <a href="{{ asset('storage/' . $content->file_path) }}" target="_blank" class="text-blue-500">{{ basename($content->file_path) }}</a> @else Tidak ada file @endif</p>
                        </div>

                        <div id="quiz_form_fields" class="mb-4 {{ old('type', $content->type) == 'quiz' ? '' : 'hidden' }}">
                            {{-- Konten form kuis akan dimuat di sini --}}
                        </div>

                        <div class="mb-4">
                            <label for="order" class="block text-sm font-medium text-gray-700">Urutan (Opsional)</label>
                            <input type="number" name="order" id="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('order', $content->order) }}">
                            @error('order')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Perbarui Konten') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/wfo9boig39silkud2152anvh7iaqnu9wf4wqh75iudy3mry6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        let globalQuestionCounter = 0;
        const quizFormFieldsContainer = document.getElementById('quiz_form_fields');
        const typeSelect = document.getElementById('type');
        const contentForm = document.getElementById('contentForm');

        let quizFormAlpineScope = null;

        document.addEventListener('DOMContentLoaded', function() {
            toggleContentTypeFields();

            contentForm.addEventListener('remove-question-from-current-tab', () => {
                if (quizFormAlpineScope) {
                    const currentTab = quizFormAlpineScope.currentQuestionTab;
                    const questions = Array.from(quizFormFieldsContainer.querySelectorAll('.question-block'));
                    if (questions[currentTab]) {
                        const questionBlockToRemove = questions[currentTab];
                        const questionIdToDelete = questionBlockToRemove.querySelector('input[name$="[id]"]').value;

                        if (questionIdToDelete) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = `questions_to_delete[]`;
                            contentForm.appendChild(hiddenInput);
                            hiddenInput.value = questionIdToDelete; // Set value after appending
                        }
                        questionBlockToRemove.remove();

                        Alpine.nextTick(() => {
                            quizFormAlpineScope.removeQuestionTab(currentTab);
                            updateQuestionIndicesAndVisibility();
                        });
                    }
                }
            });
        });

        function toggleContentTypeFields() {
            const type = typeSelect.value;
            const bodyField = document.getElementById('body_field');
            const fileUploadField = document.getElementById('file_upload_field');
            const bodyInput = document.getElementById('body');
            const fileInput = document.getElementById('file_upload');

            if (tinymce.get('body')) {
                tinymce.get('body').destroy();
            }

            bodyField.classList.add('hidden');
            fileUploadField.classList.add('hidden');
            quizFormFieldsContainer.classList.add('hidden');

            bodyInput.removeAttribute('required');
            fileInput.removeAttribute('required');

            if (type === 'text' || type === 'video') {
                bodyField.classList.remove('hidden');
                bodyInput.setAttribute('required', 'required');
                setTimeout(() => {
                    tinymce.init({
                        selector: 'textarea#body',
                        plugins: 'code table lists link image media autosave wordcount fullscreen template',
                        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link image media',
                        branding: false,
                        setup: function (editor) {
                            editor.on('change', function () {
                                editor.save();
                            });
                        }
                    });
                }, 100);
            } else if (type === 'document' || type === 'image') {
                fileUploadField.classList.remove('hidden');
                // Untuk edit, hanya wajib jika tidak ada file yang sudah ada
                if (!'{{ $content->file_path }}') {
                    fileInput.setAttribute('required', 'required');
                }
            } else if (type === 'quiz') {
                quizFormFieldsContainer.classList.remove('hidden');
                // Mengirim data kuis dari $content untuk populasi form
                const quizData = @json($content->quiz ?? null);
                loadQuizFormPartial(quizData);
            }
        }

        function loadQuizFormPartial(quizData = null) {
            quizFormFieldsContainer.innerHTML = '';
            globalQuestionCounter = 0;

            fetch('{{ route('quiz-full-form-partial') }}')
                .then(response => response.text())
                .then(html => {
                    quizFormFieldsContainer.innerHTML = html;
                    // Dapatkan referensi ke elemen root x-data yang baru dimasukkan
                    const newQuizFormRoot = quizFormFieldsContainer.firstElementChild;

                    // PENTING: Inisialisasi ulang Alpine pada elemen root yang baru
                    Alpine.initTree(newQuizFormRoot);

                    // Dapatkan scope Alpine dari elemen root yang baru diinisialisasi
                    quizFormAlpineScope = Alpine.$data(newQuizFormRoot); // Re-assign scope

                    attachQuizFormListeners(newQuizFormRoot); // Gunakan newQuizFormRoot

                    if (quizData) {
                        newQuizFormRoot.querySelector('#quiz_title').value = quizData.title || '';
                        newQuizFormRoot.querySelector('#quiz_description').value = quizData.description || '';
                        newQuizFormRoot.querySelector('#total_marks').value = quizData.total_marks || 0;
                        newQuizFormRoot.querySelector('#pass_marks').value = quizData.pass_marks || 0;
                        newQuizFormRoot.querySelector('#time_limit').value = quizData.time_limit || '';
                        newQuizFormRoot.querySelector('#quiz_status').value = quizData.status || 'draft';
                        newQuizFormRoot.querySelector('#show_answers_after_attempt').checked = quizData.show_answers_after_attempt || false;

                        if (quizData.questions && quizData.questions.length > 0) {
                            const questionsInnerContainer = newQuizFormRoot.querySelector('#questions-container-for-quiz-form');
                            if(questionsInnerContainer) questionsInnerContainer.innerHTML = '';

                            quizData.questions.forEach(q => {
                                addQuestionToQuizForm(globalQuestionCounter++, q);
                            });
                            Alpine.nextTick(() => {
                                quizFormAlpineScope.questionsCount = quizData.questions.length;
                                quizFormAlpineScope.currentQuestionTab = 0;
                            });
                        } else {
                            addQuestionToQuizForm(globalQuestionCounter++);
                            Alpine.nextTick(() => {
                                quizFormAlpineScope.questionsCount = 1;
                                quizFormAlpineScope.currentQuestionTab = 0;
                            });
                        }
                    } else {
                        addQuestionToQuizForm(globalQuestionCounter++);
                        Alpine.nextTick(() => {
                            quizFormAlpineScope.questionsCount = 1;
                            quizFormAlpineScope.currentQuestionTab = 0;
                        });
                    }
                })
                .catch(error => console.error('Error loading quiz form partial:', error));
        }

        function attachQuizFormListeners(container) {
            const addQuestionButton = container.querySelector('#add-question-to-quiz-form');
            if (addQuestionButton) {
                addQuestionButton.addEventListener('click', function() {
                    quizFormAlpineScope = Alpine.$data(quizFormFieldsContainer.firstElementChild); // Pastikan ini menunjuk ke root Alpine component
                    addQuestionToQuizForm(globalQuestionCounter++);
                });
            }
        }

        function attachQuestionListenersToQuizForm(questionBlock) {
            const selectElement = questionBlock.querySelector('[name$="[type]"]');
            const addOptionButton = questionBlock.querySelector('.add-option');

            if (selectElement) {
                selectElement.addEventListener('change', function() {
                    toggleQuestionTypeFieldsInQuizForm(this);
                });
                toggleQuestionTypeFieldsInQuizForm(selectElement);
            }

            if (addOptionButton) {
                addOptionButton.addEventListener('click', function() {
                    const currentQuestionIndex = questionBlock.dataset.questionIndex;
                    addOptionToQuizForm(currentQuestionIndex);
                });
            }

            questionBlock.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-option')) {
                    const button = event.target;
                    const optionIdToDelete = button.dataset.optionId;
                    const currentQuestionIndex = questionBlock.dataset.questionIndex;
                    if (optionIdToDelete) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `questions[${currentQuestionIndex}][options_to_delete][]`;
                        contentForm.appendChild(hiddenInput);
                        hiddenInput.value = optionIdToDelete; // Set value after appending
                    }
                    button.closest('.option-group').remove();
                }
            });
        }

        function addQuestionToQuizForm(index, questionData = null) {
            fetch('{{ route('quiz-question-partial') }}?index=' + index)
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newQuestionDiv = tempDiv.firstElementChild;
                    newQuestionDiv.dataset.questionIndex = index;

                    newQuestionDiv.setAttribute('x-show', `currentQuestionTab === ${index}`);
                    newQuestionDiv.setAttribute('x-transition:enter', 'transition ease-out duration-300');
                    newQuestionDiv.setAttribute('x-transition:enter-start', 'opacity-0 transform scale-90');
                    newQuestionDiv.setAttribute('x-transition:enter-end', 'opacity-100 transform scale-100');
                    newQuestionDiv.setAttribute('x-transition:leave', 'transition ease-in duration-200');
                    newQuestionDiv.setAttribute('x-transition:leave-start', 'opacity-100 transform scale-100');
                    newQuestionDiv.setAttribute('x-transition:leave-end', 'opacity-0 transform scale-90');

                    const replacePlaceholders = (htmlString) => {
                        let replacedHtml = htmlString.replace(/\[question_loop_index\]/g, `[${index}]`);
                        replacedHtml = replacedHtml.replace(/questions\[question_loop_index\]/g, `questions[${index}]`);
                        replacedHtml = replacedHtml.replace(/options\[question_loop_index\]\[option_loop_index\]/g, `questions[${index}][options][option_loop_index]`);
                        return replacedHtml;
                    };
                    newQuestionDiv.innerHTML = replacePlaceholders(newQuestionDiv.innerHTML);

                    if (questionData) {
                        newQuestionDiv.querySelector('[name$="[question_text]"]').value = questionData.question_text || '';
                        newQuestionDiv.querySelector('[name$="[type]"]').value = questionData.type || 'multiple_choice';
                        newQuestionDiv.querySelector('[name$="[marks]"]').value = questionData.marks || 1;
                        if (questionData.id) {
                            newQuestionDiv.querySelector('[name$="[id]"]').value = questionData.id;
                        }
                        if (questionData.type === 'true_false' && questionData.options) {
                            const correctOptionTF = questionData.options.find(opt => opt.is_correct);
                            if (correctOptionTF) {
                                const radio = newQuestionDiv.querySelector(`input[name="questions[${index}][correct_answer_tf]"][value="${correctOptionTF.option_text.toLowerCase()}"]`);
                                if (radio) radio.checked = true;
                            }
                        }
                    }

                    const questionsInnerContainer = quizFormFieldsContainer.querySelector('#questions-container-for-quiz-form');
                    questionsInnerContainer.appendChild(newQuestionDiv);

                    Alpine.initTree(newQuestionDiv); // Inisialisasi Alpine pada blok pertanyaan yang baru ditambahkan

                    attachQuestionListenersToQuizForm(newQuestionDiv);

                    if (questionData && questionData.options && questionData.type === 'multiple_choice') {
                        const optionsListContainer = newQuestionDiv.querySelector('.options-list');
                        if(optionsListContainer) optionsListContainer.innerHTML = '';

                        questionData.options.forEach(option => {
                            addOptionToQuizForm(index, option);
                        });
                    }
                    Alpine.nextTick(() => {
                        quizFormAlpineScope.questionsCount = quizFormFieldsContainer.querySelectorAll('.question-block').length;
                        quizFormAlpineScope.currentQuestionTab = index;
                        updateQuestionIndicesAndVisibility();
                    });
                })
                .catch(error => console.error('Error loading question partial:', error));
        }

        function updateQuestionIndicesAndVisibility() {
            const questions = Array.from(quizFormFieldsContainer.querySelectorAll('.question-block'));
            quizFormAlpineScope.questionsCount = questions.length;

            questions.forEach((qBlock, newIndex) => {
                qBlock.dataset.questionIndex = newIndex;
                qBlock.setAttribute('x-show', `currentQuestionTab === ${newIndex}`);

                qBlock.querySelectorAll('[name*="questions["]').forEach(input => {
                    const oldName = input.getAttribute('name');
                    const newName = oldName.replace(/questions\[\d+\]/, `questions[${newIndex}]`);
                    input.setAttribute('name', newName);
                });

                qBlock.querySelectorAll('[id*="question_text_"], [id*="question_type_"], [id*="question_marks_"], [id*="options_for_question_"], [id*="true_"], [id*="false_"]').forEach(element => {
                    const oldId = element.id;
                    const newId = oldId.replace(/_\d+$/, `_${newIndex}`);
                    element.id = newId;
                });
                qBlock.querySelectorAll('label[for*="_"]').forEach(label => {
                    const oldFor = label.getAttribute('for');
                    const newFor = oldFor.replace(/_\d+$/, `_${newIndex}`);
                    label.setAttribute('for', newFor);
                });
            });
            if (quizFormAlpineScope.currentQuestionTab >= quizFormAlpineScope.questionsCount && quizFormAlpineScope.questionsCount > 0) {
                quizFormAlpineScope.currentQuestionTab = quizFormAlpineScope.questionsCount - 1;
            } else if (quizFormAlpineScope.questionsCount === 0) {
                quizFormAlpineScope.currentQuestionTab = 0;
            }
        }

        function addOptionToQuizForm(questionIndex, optionData = null) {
            const optionsContainer = quizFormFieldsContainer.querySelector(`.question-block[data-question-index="${questionIndex}"] .options-list`);
            if (!optionsContainer) {
                console.error('Options list container not found for question index:', questionIndex);
                return;
            }

            let optionLoopIndex = optionsContainer.children.length;

            const newOptionDiv = document.createElement('div');
            newOptionDiv.classList.add('option-group', 'flex', 'items-center', 'space-x-2', 'mb-2');
            newOptionDiv.innerHTML = `
                <input type="hidden" name="questions[${questionIndex}][options][${optionLoopIndex}][id]" value="${optionData ? optionData.id : ''}">
                <input type="text" name="questions[${questionIndex}][options][${optionLoopIndex}][option_text]" placeholder="Teks Opsi" value="${optionData ? optionData.option_text : ''}" class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <input type="checkbox" name="questions[${questionIndex}][options][${optionLoopIndex}][is_correct]" value="1" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" ${optionData && optionData.is_correct ? 'checked' : ''}>
                <label class="text-sm text-gray-700">Benar</label>
                <button type="button" class="remove-option text-red-600 hover:text-red-900 text-sm" data-option-id="${optionData ? optionData.id : ''}">Hapus Opsi</button>
            `;
            optionsContainer.appendChild(newOptionDiv);

            newOptionDiv.querySelector('.remove-option').addEventListener('click', function() {
                const optionIdToDelete = this.dataset.optionId;
                if (optionIdToDelete) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `questions[${questionIndex}][options_to_delete][]`;
                    contentForm.appendChild(hiddenInput);
                    hiddenInput.value = optionIdToDelete; // Set value after appending
                }
                newOptionDiv.remove();
            });
        }

        function toggleQuestionTypeFieldsInQuizForm(selectElement) {
            const questionBlock = selectElement.closest('.question-block');
            const type = selectElement.value;
            const optionsContainer = questionBlock.querySelector('.options-container');
            const addOptionButton = questionBlock.querySelector('.add-option');
            const trueFalseOptions = questionBlock.querySelector('.true-false-options');
            const optionTextInputs = questionBlock.querySelectorAll('.options-list input[name$="[option_text]"]');
            const trueFalseRadios = trueFalseOptions ? trueFalseOptions.querySelectorAll('input[type="radio"]') : [];

            if (type === 'multiple_choice') {
                optionsContainer.classList.remove('hidden');
                addOptionButton.classList.remove('hidden');
                if (trueFalseOptions) trueFalseOptions.classList.add('hidden');

                optionTextInputs.forEach(input => {
                    input.setAttribute('required', 'required');
                });
                trueFalseRadios.forEach(radio => radio.removeAttribute('required'));
            } else if (type === 'true_false') {
                optionsContainer.classList.add('hidden');
                addOptionButton.classList.add('hidden');
                if (trueFalseOptions) trueFalseOptions.classList.remove('hidden');

                optionTextInputs.forEach(input => {
                    input.removeAttribute('required');
                });
                trueFalseRadios.forEach(radio => radio.setAttribute('required', 'required'));
            }
        }
    </script>
</x-app-layout>