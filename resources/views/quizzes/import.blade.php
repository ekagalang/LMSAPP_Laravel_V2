<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white -mx-6 -mt-6 mb-6 px-6 py-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 p-3 rounded-lg">
                        <i class="fas fa-file-excel text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold">Import Kuis dari Excel</h2>
                        <p class="text-indigo-100 mt-1">Upload file Excel untuk membuat kuis secara batch</p>
                    </div>
                </div>
                <a href="{{ route('quizzes.index') }}"
                   class="bg-white/20 text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/30 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">Sukses!</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                        <div>
                            <p class="font-bold">Error!</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow-md" role="alert">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-xl mr-3 mt-1"></i>
                        <div class="flex-1">
                            <p class="font-bold mb-2">Peringatan Import:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach(session('import_errors') as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Instructions Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-start space-x-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Panduan Import Kuis</h3>
                        <div class="space-y-2 text-gray-600">
                            <p><i class="fas fa-check text-green-500 mr-2"></i> Download template Excel terlebih dahulu</p>
                            <p><i class="fas fa-check text-green-500 mr-2"></i> Isi data quiz sesuai format yang ada</p>
                            <p><i class="fas fa-check text-green-500 mr-2"></i> Satu quiz bisa memiliki banyak pertanyaan</p>
                            <p><i class="fas fa-check text-green-500 mr-2"></i> Untuk pertanyaan yang satu quiz, gunakan judul quiz yang sama</p>
                            <p><i class="fas fa-check text-green-500 mr-2"></i> Tipe pertanyaan: <code class="bg-gray-200 px-2 py-1 rounded">multiple_choice</code> atau <code class="bg-gray-200 px-2 py-1 rounded">true_false</code></p>
                            <p><i class="fas fa-check text-green-500 mr-2"></i> Status: <code class="bg-gray-200 px-2 py-1 rounded">draft</code> atau <code class="bg-gray-200 px-2 py-1 rounded">published</code></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Template Card -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-4 rounded-lg">
                            <i class="fas fa-download text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-1">Template Excel</h3>
                            <p class="text-green-100">Download template untuk memulai import kuis</p>
                        </div>
                    </div>
                    <a href="{{ route('quizzes.download-template') }}"
                       class="bg-white text-green-600 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:bg-gray-50 transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-file-excel"></i>
                        <span>Download Template</span>
                    </a>
                </div>
            </div>

            <!-- Import Form Card -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-upload text-indigo-600 mr-3"></i>
                    Upload File Excel
                </h3>

                <form action="{{ route('quizzes.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Lesson Selection -->
                    <div class="mb-6">
                        <label for="lesson_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book mr-2 text-indigo-600"></i>Pilih Lesson
                        </label>
                        <select name="lesson_id" id="lesson_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 @error('lesson_id') border-red-500 @enderror"
                                required>
                            <option value="">-- Pilih Lesson --</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }} - {{ $lesson->course->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('lesson_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-file-upload mr-2 text-indigo-600"></i>File Excel
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200 @error('file') border-red-500 @enderror">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                    <p class="text-xs text-gray-500">File Excel (XLSX, XLS, CSV) maksimal 2MB</p>
                                    <p class="text-xs text-gray-400 mt-2" id="file-name"></p>
                                </div>
                                <input id="file" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)" />
                            </label>
                        </div>
                        @error('file')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('quizzes.index') }}"
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-200">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit"
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200">
                            <i class="fas fa-upload mr-2"></i>Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0]?.name;
            const fileNameDisplay = document.getElementById('file-name');
            if (fileName) {
                fileNameDisplay.textContent = 'File terpilih: ' + fileName;
                fileNameDisplay.classList.remove('text-gray-400');
                fileNameDisplay.classList.add('text-indigo-600', 'font-semibold');
            }
        }
    </script>
</x-app-layout>
