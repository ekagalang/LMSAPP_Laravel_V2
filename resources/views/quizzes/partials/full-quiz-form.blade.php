<div x-data="{
    currentQuestionTab: 0,
    questionsCount: 0, // Akan diupdate oleh JS utama
    init() {
        // Callback setelah pertanyaan dimuat untuk update questionsCount
        this.$watch('questionsCount', (val) => {
            if (this.currentQuestionTab >= val && val > 0) {
                this.currentQuestionTab = val - 1; // Kembali ke tab terakhir jika tab saat ini melebihi jumlah
            } else if (val === 0) {
                this.currentQuestionTab = 0; // Reset ke 0 jika tidak ada pertanyaan
            }
        });

        // Inisialisasi awal jumlah pertanyaan jika ada yang sudah dimuat (misal pada edit)
        // Ini mungkin perlu disinkronkan dengan globalQuestionCounter dari JS utama
        const initialQuestions = document.querySelectorAll('#questions-container-for-quiz-form .question-block');
        this.questionsCount = initialQuestions.length;
        if (this.questionsCount > 0) {
            this.currentQuestionTab = 0;
        } else {
            this.currentQuestionTab = 0; // Default to 0 if no questions initially
        }
    },
    showQuestion(index) {
        this.currentQuestionTab = index;
    },
    addQuestionTab(newIndex) {
        this.questionsCount++; // Cukup increment di sini, karena addQuestionToQuizForm akan memberikan indeks baru
        this.currentQuestionTab = newIndex; // Pindah ke tab pertanyaan baru
    },
    removeQuestionTab(removedIndex) {
        this.questionsCount--;
        if (this.currentQuestionTab > removedIndex) {
            this.currentQuestionTab--;
        } else if (this.currentQuestionTab === removedIndex && this.questionsCount > 0) {
            this.currentQuestionTab = Math.max(0, this.questionsCount - 1); // Pindah ke tab sebelumnya jika yang dihapus adalah tab aktif
        } else if (this.questionsCount === 0) {
            this.currentQuestionTab = 0; // Reset ke 0 jika tidak ada pertanyaan
        }
    }
}" class="border border-gray-200 p-6 rounded-lg bg-gray-50">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Detail Kuis</h4>

    <div class="mb-4">
        <label for="quiz_title" class="block text-sm font-medium text-gray-700">Judul Kuis</label>
        <input type="text" name="quiz_title" id="quiz_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('quiz_title') }}" required>
        @error('quiz_title')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="quiz_description" class="block text-sm font-medium text-gray-700">Deskripsi Kuis (Opsional)</label>
        <textarea name="quiz_description" id="quiz_description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('quiz_description') }}</textarea>
        @error('quiz_description')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label for="total_marks" class="block text-sm font-medium text-gray-700">Total Nilai Maksimal</label>
            <input type="number" name="total_marks" id="total_marks" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('total_marks', 0) }}" required min="0">
            @error('total_marks')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="pass_marks" class="block text-sm font-medium text-gray-700">Nilai Minimal Lulus</label>
            <input type="number" name="pass_marks" id="pass_marks" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('pass_marks', 0) }}" required min="0">
            @error('pass_marks')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="time_limit" class="block text-sm font-medium text-gray-700">Batas Waktu (Menit, Opsional)</label>
            <input type="number" name="time_limit" id="time_limit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('time_limit') }}" min="1">
            @error('time_limit')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label for="quiz_status" class="block text-sm font-medium text-gray-700">Status Kuis</label>
        <select name="quiz_status" id="quiz_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            <option value="draft" {{ old('quiz_status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('quiz_status', '') == 'published' ? 'selected' : '' }}>Published</option>
        </select>
        @error('quiz_status')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <div class="flex items-center">
            <input type="checkbox" name="show_answers_after_attempt" id="show_answers_after_attempt" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('show_answers_after_attempt') ? 'checked' : '' }}>
            <label for="show_answers_after_attempt" class="ml-2 text-sm text-gray-700">Tampilkan jawaban benar/salah setelah percobaan kuis</label>
        </div>
    </div>

    {{-- Bagian Pertanyaan --}}
    <h3 class="text-xl font-bold text-gray-900 mb-4 border-t pt-4">Pertanyaan Kuis</h3>

    {{-- Navigasi Tab Pertanyaan --}}
    <div class="flex border-b border-gray-200 mb-4 overflow-x-auto whitespace-nowrap">
        <template x-for="index in questionsCount" :key="index">
            <button type="button" @click="showQuestion(index - 1)" 
                    :class="{ 'border-indigo-500 text-indigo-600': currentQuestionTab === (index - 1), '...' : currentQuestionTab !== (index - 1) }"
                    class="py-2 px-4 border-b-2 font-medium text-sm">
                <span x-text="index"></span>
            </button>
        </template>
    </div>

    <div id="questions-container-for-quiz-form">
        {{-- Pertanyaan akan ditambahkan ke sini oleh JavaScript --}}
    </div>

    <div class="mt-6 flex justify-between items-center">
        <button type="button" id="add-question-to-quiz-form" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('Tambah Pertanyaan') }}
        </button>
        {{-- Tombol Hapus Pertanyaan Saat Ini --}}
        <button type="button" id="remove-current-question" x-show="questionsCount > 1" @click="$dispatch('remove-question-from-current-tab')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('Hapus Pertanyaan Ini') }}
        </button>
    </div>
</div>