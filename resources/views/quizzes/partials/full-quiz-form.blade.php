<div class="border border-gray-200 p-6 rounded-lg bg-gray-50/50 mt-4">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Detail Kuis</h4>
    
    {{-- Bagian Detail Kuis (Tidak berubah) --}}
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Judul Kuis</label>
            <input type="text" name="quiz[title]" x-model="content.quiz.title" class="mt-1 block w-full rounded-md" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Deskripsi</label>
            <textarea name="quiz[description]" x-model="content.quiz.description" rows="3" class="mt-1 block w-full rounded-md"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Persentase Lulus (%)</label>
                <input type="number"
                       name="quiz[passing_percentage]"
                       x-model.number="content.quiz.passing_percentage"
                       class="mt-1 block w-full rounded-md"
                       min="0"
                       max="100"
                       required>
                <p class="text-xs text-gray-500 mt-1">Nilai minimum untuk lulus (0-100%)</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="quiz[status]" x-model="content.quiz.status" class="mt-1 block w-full rounded-md">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
        </div>
        <div class="space-y-3">
            <label class="flex items-center">
                <input type="hidden" name="quiz[show_answers_after_attempt]" value="0">
                <input type="checkbox" name="quiz[show_answers_after_attempt]" value="1" x-model="content.quiz.show_answers_after_attempt" class="rounded">
                <span class="ml-2 text-sm">Tampilkan jawaban setelah percobaan</span>
            </label>
            <label class="flex items-center">
                <input type="hidden" name="quiz[enable_leaderboard]" value="0">
                <input type="checkbox" name="quiz[enable_leaderboard]" value="1" x-model="content.quiz.enable_leaderboard" class="rounded">
                <span class="ml-2 text-sm">Aktifkan Leaderboard</span>
            </label>
        </div>
    </div>

    <hr class="my-6">

    <h3 class="text-xl font-bold text-gray-900 mb-4">Daftar Pertanyaan</h3>
    
    <div class="space-y-4">
        <template x-for="(question, qIndex) in content.quiz.questions" :key="qIndex">
            <div class="bg-white rounded-lg border">
                <div @click="question.open = !question.open" class="flex justify-between items-center p-4 cursor-pointer">
                    <h5 class="font-bold text-md" x-text="question.question_text || `Pertanyaan #${qIndex + 1}`"></h5>
                    <div class="flex items-center">
                        <button type="button" @click.stop="removeQuestion(qIndex)" class="text-red-600 hover:text-red-800 mr-4">&times; Hapus</button>
                        <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': question.open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                
                <div x-show="question.open" x-collapse class="p-4 border-t">
                    <input type="hidden" :name="`quiz[questions][${qIndex}][id]`" x-model="question.id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Teks Pertanyaan</label>
                        <textarea :name="`quiz[questions][${qIndex}][question_text]`" x-model="question.question_text" rows="2" class="mt-1 block w-full rounded-md" required></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium">Tipe</label>
                            <select :name="`quiz[questions][${qIndex}][type]`" x-model="question.type" class="mt-1 block w-full rounded-md">
                                <option value="multiple_choice">Pilihan Ganda</option>
                                <option value="true_false">Benar/Salah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Nilai</label>
                            <input type="number" :name="`quiz[questions][${qIndex}][marks]`" x-model.number="question.marks" class="mt-1 block w-full rounded-md" required min="1">
                        </div>
                    </div>

                   <div x-show="question.type === 'multiple_choice'" class="mt-4 border-t pt-4">
                        <div class="space-y-2">
                            <template x-for="(option, oIndex) in question.options" :key="oIndex">
                                <div class="flex items-center space-x-2">
                                    <input type="hidden" :name="`quiz[questions][${qIndex}][options][${oIndex}][id]`" x-model="option.id">
                                    <input type="text" :name="`quiz[questions][${qIndex}][options][${oIndex}][option_text]`" x-model="option.option_text" class="flex-grow rounded-md" placeholder="Teks opsi" required>
                                    
                                    {{-- ✅ PERBAIKAN: Checkbox untuk Pilihan Ganda --}}
                                    <input type="hidden" :name="`quiz[questions][${qIndex}][options][${oIndex}][is_correct]`" value="false">
                                    <input type="checkbox" :name="`quiz[questions][${qIndex}][options][${oIndex}][is_correct]`" value="true" x-model="option.is_correct" class="rounded">
                                    
                                    <label class="text-sm">Benar</label>
                                    <button type="button" @click="removeOption(qIndex, oIndex)" class="text-red-500 hover:text-red-700">&times;</button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addOption(qIndex)" class="mt-2 text-sm text-blue-600 hover:underline">+ Tambah Opsi</button>
                    </div>

                    {{-- ✅ PERBAIKAN: Radio Button untuk Benar/Salah --}}
                    <div x-show="question.type === 'true_false'" class="mt-4 border-t pt-4">
                        <h5 class="text-md font-semibold text-gray-700 mb-2">Jawaban Benar:</h5>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" :name="`quiz[questions][${qIndex}][correct_answer_tf]`" value="true" x-model="question.correct_answer_tf">
                                <span class="ml-2">True</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" :name="`quiz[questions][${qIndex}][correct_answer_tf]`" value="false" x-model="question.correct_answer_tf">
                                <span class="ml-2">False</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="mt-6">
        <button type="button" @click="addQuestion()" class="w-full px-4 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 border border-dashed">Tambah Pertanyaan</button>
    </div>
</div>