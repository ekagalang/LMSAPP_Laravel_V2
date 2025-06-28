@php
    $q_index = $question_loop_index ?? 'new_' . uniqid();
    $question_text = $question->question_text ?? '';
    $question_type = $question->type ?? 'multiple_choice';
    $question_marks = $question->marks ?? 1;
    $is_edit_mode = isset($question) && isset($question->id);
@endphp

{{-- Menggunakan Alpine.js untuk fungsionalitas expand/collapse --}}
<div class="question-block bg-gray-100 p-6 rounded-lg shadow-inner mb-4" data-question-index="{{ $q_index }}" x-data="{ open: {{ $is_edit_mode ? 'false' : 'true' }} }">
    <div class="flex justify-between items-center mb-4 cursor-pointer" @click="open = !open">
        <h4 class="text-lg font-semibold text-gray-800">
            Pertanyaan: <span x-text="document.getElementById('question_text_{{ $q_index }}').value || 'Pertanyaan Baru'"></span>
            <template x-if="!open">
                <svg class="w-4 h-4 inline-block ml-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </template>
            <template x-if="open">
                <svg class="w-4 h-4 inline-block ml-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </template>
        </h4>
        <button type="button" class="remove-question text-red-600 hover:text-red-900 text-sm" data-question-id="{{ $question->id ?? '' }}" @click.stop="">Hapus Pertanyaan Ini</button>
    </div>

    <div x-show="open" x-collapse>
        <input type="hidden" name="questions[{{ $q_index }}][id]" value="{{ $question->id ?? '' }}">

        <div class="mb-4">
            <label for="question_text_{{ $q_index }}" class="block text-sm font-medium text-gray-700">Teks Pertanyaan</label>
            <textarea name="questions[{{ $q_index }}][question_text]" id="question_text_{{ $q_index }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old("questions.{$q_index}.question_text", $question_text) }}</textarea>
            @error("questions.{$q_index}.question_text")
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="question_type_{{ $q_index }}" class="block text-sm font-medium text-gray-700">Tipe Pertanyaan</label>
                <select name="questions[{{ $q_index }}][type]" id="question_type_{{ $q_index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleQuestionTypeFieldsInQuizForm(this)">
                    <option value="multiple_choice" {{ old("questions.{$q_index}.type", $question_type) == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="true_false" {{ old("questions.{$q_index}.type", $question_type) == 'true_false' ? 'selected' : '' }}>Benar/Salah</option>
                </select>
                @error("questions.{$q_index}.type")
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="question_marks_{{ $q_index }}" class="block text-sm font-medium text-gray-700">Nilai Pertanyaan</label>
                <input type="number" name="questions[{{ $q_index }}][marks]" id="question_marks_{{ $q_index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old("questions.{$q_index}.marks", $question_marks) }}" required min="1">
                @error("questions.{$q_index}.marks")
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Kontainer Opsi Pilihan Ganda --}}
        <div class="options-container border-t border-gray-200 pt-4 mt-4 {{ old("questions.{$q_index}.type", $question_type) == 'multiple_choice' ? '' : 'hidden' }}">
            <h5 class="text-md font-semibold text-gray-700 mb-3">Opsi Jawaban:</h5>
            <div class="options-list" id="options_for_question_{{ $q_index }}">
                @if ($is_edit_mode && $question->options->isNotEmpty() && $question_type == 'multiple_choice')
                    @foreach ($question->options as $option)
                        @php $o_index = $option->id ?? 'new_' . uniqid(); @endphp
                        <div class="option-group flex items-center space-x-2 mb-2" data-option-index="{{ $o_index }}">
                            <input type="hidden" name="questions[{{ $q_index }}][options][{{ $o_index }}][id]" value="{{ $option->id ?? '' }}">
                            <input type="text" name="questions[{{ $q_index }}][options][{{ $o_index }}][option_text]" placeholder="Teks Opsi" value="{{ old("questions.{$q_index}.options.{$o_index}.option_text", $option->option_text) }}" class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <input type="checkbox" name="questions[{{ $q_index }}][options][{{ $o_index }}][is_correct]" value="1" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" {{ old("questions.{$q_index}.options.{$o_index}.is_correct", $option->is_correct) ? 'checked' : '' }}>
                            <label class="text-sm text-gray-700">Benar</label>
                            <button type="button" class="remove-option text-red-600 hover:text-red-900 text-sm" data-option-id="{{ $option->id ?? '' }}">Hapus Opsi</button>
                        </div>
                        @error("questions.{$q_index}.options.{$o_index}.option_text")
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    @endforeach
                @endif
            </div>
            <button type="button" class="add-option mt-2 inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Tambah Opsi
            </button>
        </div>

        {{-- Opsi Benar/Salah --}}
        <div class="true-false-options border-t border-gray-200 pt-4 mt-4 {{ old("questions.{$q_index}.type", $question_type) == 'true_false' ? '' : 'hidden' }}">
            <h5 class="text-md font-semibold text-gray-700 mb-3">Jawaban Benar/Salah:</h5>
            <div class="flex items-center space-x-4">
                {{-- Gunakan id dari opsi yang benar jika dalam mode edit dan tipe true_false --}}
                @php
                    $correctTFOptionValue = null;
                    if ($is_edit_mode && $question_type == 'true_false' && $question->options->isNotEmpty()) {
                        $correctTFOption = $question->options->where('is_correct', true)->first();
                        $correctTFOptionValue = $correctTFOption ? strtolower($correctTFOption->option_text) : null;
                    }
                @endphp
                <input type="radio" name="questions[{{ $q_index }}][correct_answer_tf]" id="true_{{ $q_index }}" value="true" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old("questions.{$q_index}.correct_answer_tf", $correctTFOptionValue) == 'true' ? 'checked' : '' }} required>
                <label for="true_{{ $q_index }}" class="text-sm text-gray-700">True</label>

                <input type="radio" name="questions[{{ $q_index }}][correct_answer_tf]" id="false_{{ $q_index }}" value="false" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old("questions.{$q_index}.correct_answer_tf", $correctTFOptionValue) == 'false' ? 'checked' : '' }} required>
                <label for="false_{{ $q_index }}" class="text-sm text-gray-700">False</label>
            </div>
            @error("questions.{$q_index}.correct_answer_tf")
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>