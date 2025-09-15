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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Total Nilai</label>
                <input type="number" name="quiz[total_marks]" x-model.number="content.quiz.total_marks" class="mt-1 block w-full rounded-md" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Nilai Lulus</label>
                <input type="number" name="quiz[pass_marks]" x-model.number="content.quiz.pass_marks" class="mt-1 block w-full rounded-md" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="quiz[status]" x-model="content.quiz.status" class="mt-1 block w-full rounded-md">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
        </div>
        <div>
            <label class="flex items-center">
                <input type="hidden" name="quiz[show_answers_after_attempt]" value="0">
                <input type="checkbox" name="quiz[show_answers_after_attempt]" value="1" x-model="content.quiz.show_answers_after_attempt" class="rounded">
                <span class="ml-2 text-sm">Tampilkan jawaban setelah percobaan</span>
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
                                <option value="multiple_choice">📋 Pilihan Ganda</option>
                                <option value="true_false">✅ Benar/Salah</option>
                                <option value="fill_blank">✏️ Fill in the Blank</option>
                                <option value="listening_comprehension">👂 Listening Comprehension</option>
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

                    {{-- ✅ BARU: Fill in the Blank --}}
                    <div x-show="question.type === 'fill_blank'" class="mt-4 border-t pt-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h5 class="text-sm font-semibold text-blue-800 mb-2">💡 Cara Membuat Fill in the Blank:</h5>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p>• Gunakan <code class="bg-blue-100 px-1 rounded">____</code> (underscore 4x) untuk menandai tempat jawaban</p>
                                <p>• Contoh: "The capital of Indonesia is ____"</p>
                                <p>• Atau: "Fill the blanks: I ____ to school every day"</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban Benar</label>
                                <input type="text"
                                       :name="`quiz[questions][${qIndex}][correct_answer]`"
                                       x-model="question.correct_answer"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                       placeholder="Masukkan jawaban yang benar..."
                                       required>
                                <p class="text-xs text-gray-500 mt-1">Jawaban akan dicocokkan (case-insensitive)</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban Alternatif (Opsional)</label>
                                <input type="text"
                                       :name="`quiz[questions][${qIndex}][alternative_answers]`"
                                       x-model="question.alternative_answers"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                       placeholder="jakarta|dki jakarta|ibu kota (pisahkan dengan |)">
                                <p class="text-xs text-gray-500 mt-1">Pisahkan dengan | untuk multiple jawaban benar</p>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ BARU: Listening Comprehension --}}
                    <div x-show="question.type === 'listening_comprehension'" class="mt-4 border-t pt-4">
                        <div class="bg-teal-50 border border-teal-200 rounded-lg p-4 mb-4">
                            <h5 class="text-sm font-semibold text-teal-800 mb-2">👂 Listening Comprehension</h5>
                            <div class="text-sm text-teal-700 space-y-2">
                                <p>• <strong>Soal pemahaman mendengar</strong> yang membutuhkan analisis audio</p>
                                <p>• Siswa mendengarkan audio dan menjawab berdasarkan apa yang didengar</p>

                                <div class="mt-3 p-3 bg-teal-100 rounded-md">
                                    <p class="font-medium text-teal-800 mb-1">Contoh:</p>
                                    <p class="text-xs">📝 <strong>Teks bebas:</strong> "Apa tema utama yang dibahas dalam audio?"</p>
                                    <p class="text-xs">📋 <strong>Multiple Choice:</strong> "Siapa yang berbicara di audio?" → A) Guru B) Dokter C) Pilot</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Jawaban</label>
                                <select :name="`quiz[questions][${qIndex}][comprehension_type]`"
                                        x-model="question.comprehension_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-teal-500 focus:ring-2 focus:ring-teal-200">
                                    <option value="text">📝 Jawaban Teks Bebas</option>
                                    <option value="multiple_choice">📋 Multiple Choice</option>
                                </select>
                            </div>

                            <div x-show="question.comprehension_type === 'text'">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>💡 Teks Bebas:</strong> Siswa akan menulis jawaban sendiri. Instructor menilai secara manual.
                                    </p>
                                </div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban yang Diharapkan (Panduan Penilaian)</label>
                                <textarea :name="`quiz[questions][${qIndex}][expected_answer]`"
                                          x-model="question.expected_answer"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-teal-500 focus:ring-2 focus:ring-teal-200"
                                          placeholder="Contoh: Siswa harus menyebutkan 3 poin utama yang dibahas pembicara..."></textarea>
                                <p class="text-xs text-gray-500 mt-1">Ini sebagai referensi untuk instructor saat menilai jawaban siswa</p>
                            </div>

                            <div x-show="question.comprehension_type === 'multiple_choice'">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                                    <p class="text-sm text-green-800">
                                        <strong>📋 Multiple Choice:</strong> Siswa memilih satu jawaban benar dari pilihan yang tersedia. Penilaian otomatis.
                                    </p>
                                </div>
                                <h6 class="text-sm font-semibold text-gray-700 mb-3">Buat Pilihan Jawaban</h6>

                                <!-- Initialize options if comprehension_type is multiple_choice -->
                                <div x-init="
                                    // Initialize options on load
                                    if (question.comprehension_type === 'multiple_choice' && (!question.options || question.options.length === 0)) {
                                        question.options = [
                                            { id: null, option_text: '', is_correct: false },
                                            { id: null, option_text: '', is_correct: false },
                                            { id: null, option_text: '', is_correct: false },
                                            { id: null, option_text: '', is_correct: false }
                                        ];
                                    }

                                    // Watch for changes in comprehension_type
                                    $watch('question.comprehension_type', (newType) => {
                                        if (newType === 'multiple_choice' && (!question.options || question.options.length === 0)) {
                                            question.options = [
                                                { id: null, option_text: '', is_correct: false },
                                                { id: null, option_text: '', is_correct: false },
                                                { id: null, option_text: '', is_correct: false },
                                                { id: null, option_text: '', is_correct: false }
                                            ];
                                        }
                                    });
                                " class="space-y-2">
                                    <template x-for="(option, oIndex) in question.options" :key="oIndex">
                                        <div class="flex items-center space-x-2 bg-gray-50 p-3 rounded-lg">
                                            <input type="hidden" :name="`quiz[questions][${qIndex}][options][${oIndex}][id]`" x-model="option.id">
                                            <input type="text"
                                                   :name="`quiz[questions][${qIndex}][options][${oIndex}][option_text]`"
                                                   x-model="option.option_text"
                                                   class="flex-grow rounded-md border-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-200"
                                                   placeholder="Masukkan pilihan jawaban..."
                                                   required>

                                            <!-- Checkbox untuk menandai jawaban benar -->
                                            <input type="hidden" :name="`quiz[questions][${qIndex}][options][${oIndex}][is_correct]`" value="false">
                                            <input type="checkbox"
                                                   :name="`quiz[questions][${qIndex}][options][${oIndex}][is_correct]`"
                                                   value="true"
                                                   x-model="option.is_correct"
                                                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">

                                            <label class="text-sm text-gray-700 font-medium">Benar</label>
                                            <button type="button"
                                                    @click="removeOption(qIndex, oIndex)"
                                                    class="text-red-500 hover:text-red-700 font-bold text-lg"
                                                    title="Hapus opsi">×</button>
                                        </div>
                                    </template>

                                    <button type="button"
                                            @click="addOption(qIndex)"
                                            class="w-full mt-2 px-4 py-2 border-2 border-dashed border-teal-300 text-teal-600 rounded-lg hover:border-teal-400 hover:bg-teal-50 transition-all duration-200">
                                        + Tambah Pilihan Jawaban
                                    </button>
                                </div>
                            </div>
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