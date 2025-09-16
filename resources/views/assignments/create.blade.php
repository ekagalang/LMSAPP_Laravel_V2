<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Buat Tugas Baru') }}
            </h2>
            <a href="{{ route('assignments.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Terjadi kesalahan:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informasi Dasar</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Judul Tugas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('title') border-red-500 @enderror"
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Deskripsi
                                    </label>
                                    <textarea class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror"
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Instruksi Pengerjaan
                                    </label>
                                    <textarea class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('instructions') border-red-500 @enderror"
                                              id="instructions" name="instructions" rows="5">{{ old('instructions') }}</textarea>
                                    @error('instructions')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Berikan instruksi detail tentang bagaimana mengerjakan tugas ini.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submission Settings -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pengaturan Pengumpulan</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="submission_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Tipe Pengumpulan <span class="text-red-500">*</span>
                                    </label>
                                    <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('submission_type') border-red-500 @enderror"
                                            id="submission_type" name="submission_type" required>
                                        <option value="">Pilih tipe pengumpulan</option>
                                        <option value="file" {{ old('submission_type') === 'file' ? 'selected' : '' }}>File saja</option>
                                        <option value="link" {{ old('submission_type') === 'link' ? 'selected' : '' }}>Link saja</option>
                                        <option value="both" {{ old('submission_type') === 'both' ? 'selected' : '' }}>File atau Link</option>
                                    </select>
                                    @error('submission_type')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- File Settings -->
                                <div id="file-settings" class="hidden space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="max_files" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Maksimal File
                                            </label>
                                            <input type="number"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('max_files') border-red-500 @enderror"
                                                   id="max_files" name="max_files" value="{{ old('max_files', 1) }}"
                                                   min="1" max="10">
                                            @error('max_files')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="max_file_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Maksimal Ukuran File (MB)
                                            </label>
                                            <input type="number"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('max_file_size') border-red-500 @enderror"
                                                   id="max_file_size" name="max_file_size" value="{{ old('max_file_size', 50) }}"
                                                   min="1" max="1024">
                                            @error('max_file_size')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                            Tipe File yang Diizinkan
                                        </label>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                            @php
                                                $fileTypes = [
                                                    'pdf' => 'PDF', 'doc' => 'Word (DOC)', 'docx' => 'Word (DOCX)',
                                                    'xls' => 'Excel (XLS)', 'xlsx' => 'Excel (XLSX)',
                                                    'ppt' => 'PowerPoint (PPT)', 'pptx' => 'PowerPoint (PPTX)',
                                                    'txt' => 'Text', 'jpg' => 'JPEG', 'jpeg' => 'JPEG', 'png' => 'PNG',
                                                    'gif' => 'GIF', 'mp4' => 'MP4', 'mov' => 'MOV', 'avi' => 'AVI',
                                                    'mkv' => 'MKV', 'mp3' => 'MP3', 'wav' => 'WAV',
                                                    'zip' => 'ZIP', 'rar' => 'RAR'
                                                ];
                                                $oldFileTypes = old('allowed_file_types', []);
                                            @endphp
                                            @foreach($fileTypes as $ext => $label)
                                                <div class="flex items-center">
                                                    <input type="checkbox"
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                                           name="allowed_file_types[]" value="{{ $ext }}"
                                                           id="file_{{ $ext }}"
                                                           {{ in_array($ext, $oldFileTypes) ? 'checked' : '' }}>
                                                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300" for="file_{{ $ext }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('allowed_file_types')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Schedule & Grading -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Jadwal & Penilaian</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Tenggat Waktu
                                    </label>
                                    <input type="datetime-local"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('due_date') border-red-500 @enderror"
                                           id="due_date" name="due_date" value="{{ old('due_date') }}">
                                    @error('due_date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_points" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Poin Maksimal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('max_points') border-red-500 @enderror"
                                           id="max_points" name="max_points" value="{{ old('max_points', 100) }}"
                                           min="1" max="1000" required>
                                    @error('max_points')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Late Submission -->
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                               id="allow_late_submission" name="allow_late_submission" value="1"
                                               {{ old('allow_late_submission') ? 'checked' : '' }}>
                                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-300" for="allow_late_submission">
                                            Izinkan pengumpulan terlambat
                                        </label>
                                    </div>
                                </div>

                                <div id="late-settings" class="hidden space-y-4">
                                    <div>
                                        <label for="late_submission_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Batas akhir pengumpulan terlambat
                                        </label>
                                        <input type="datetime-local"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('late_submission_until') border-red-500 @enderror"
                                               id="late_submission_until" name="late_submission_until" value="{{ old('late_submission_until') }}">
                                        @error('late_submission_until')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="late_penalty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Penalti keterlambatan (%)
                                        </label>
                                        <input type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 @error('late_penalty') border-red-500 @enderror"
                                               id="late_penalty" name="late_penalty" value="{{ old('late_penalty', 0) }}"
                                               min="0" max="100" step="0.1">
                                        @error('late_penalty')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Visibility -->
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                               id="show_to_students" name="show_to_students" value="1"
                                               {{ old('show_to_students', true) ? 'checked' : '' }}>
                                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-300" for="show_to_students">
                                            Tampilkan ke siswa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-3">
                                <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Buat Tugas
                                </button>
                                <a href="{{ route('assignments.index') }}"
                                   class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submissionType = document.getElementById('submission_type');
            const fileSettings = document.getElementById('file-settings');
            const allowLateSubmission = document.getElementById('allow_late_submission');
            const lateSettings = document.getElementById('late-settings');

            // Toggle file settings based on submission type
            function toggleFileSettings() {
                const value = submissionType.value;
                if (value === 'file' || value === 'both') {
                    fileSettings.classList.remove('hidden');
                } else {
                    fileSettings.classList.add('hidden');
                }
            }

            // Toggle late submission settings
            function toggleLateSettings() {
                if (allowLateSubmission.checked) {
                    lateSettings.classList.remove('hidden');
                } else {
                    lateSettings.classList.add('hidden');
                }
            }

            submissionType.addEventListener('change', toggleFileSettings);
            allowLateSubmission.addEventListener('change', toggleLateSettings);

            // Initialize on page load
            toggleFileSettings();
            toggleLateSettings();

            // Convert max_file_size from MB to bytes for backend
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const maxFileSize = document.getElementById('max_file_size');
                if (maxFileSize.value) {
                    maxFileSize.value = maxFileSize.value * 1024 * 1024; // Convert MB to bytes
                }
            });
        });
    </script>
    @endpush
</x-app-layout>