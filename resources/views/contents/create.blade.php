<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('courses.show', $lesson->course) }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('Kembali ke Detail Kursus') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-2">
                    {{ __('Tambah Konten Baru untuk Pelajaran:') }} {{ $lesson->title }}
                </h2>
                <p class="text-sm text-gray-600">Kursus: {{ $lesson->course->title }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="contentForm" method="POST" action="{{ route('lessons.contents.store', $lesson) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Konten</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('title') }}" required autofocus>
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Konten</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleContentTypeFields()">
                                <option value="text" @selected(old('type') == 'text')>Teks</option>
                                <option value="video" @selected(old('type') == 'video')>Video (URL YouTube/Vimeo)</option>
                                <option value="document" @selected(old('type') == 'document')>Dokumen (PDF, DOCX, PPTX)</option>
                                <option value="image" @selected(old('type') == 'image')>Gambar (JPG, PNG)</option>
                                <option value="quiz" @selected(old('type') == 'quiz')>Kuis</option>
                                <option value="essay" @selected(old('type') == 'essay')>Esai</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="body_field" class="mb-4">
                            <label for="body" class="block text-sm font-medium text-gray-700">Isi Konten / URL</label>
                            <textarea name="body" id="body" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('body') }}</textarea>
                            @error('body')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Untuk Video, masukkan URL YouTube/Vimeo. Untuk Esai, masukkan pertanyaan di sini.</p>
                        </div>

                        <div id="file_upload_field" class="mb-4 hidden">
                            <label for="file_upload" class="block text-sm font-medium text-gray-700">Unggah File</label>
                            <input type="file" name="file_upload" id="file_upload" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                            @error('file_upload')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Maksimal 10MB.</p>
                        </div>

                        <div id="quiz_form_fields" class="mb-4 hidden">
                            {{-- Konten form kuis akan dimuat di sini --}}
                        </div>

                        <div class="mb-4">
                            <label for="order" class="block text-sm font-medium text-gray-700">Urutan (Opsional, Default ke Akhir)</label>
                            <input type="number" name="order" id="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('order') }}">
                            @error('order')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            {{-- ✅ PERBAIKAN: Tombol diubah untuk memanggil fungsi submitCreateForm() --}}
                            <button type="button" onclick="submitCreateForm()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Simpan Konten') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/wfo9boig39silkud2152anvh7iaqnu9wf4wqh75iudy3mry6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // ✅ PERBAIKAN: Fungsi baru untuk menangani submit pada halaman create
        function submitCreateForm() {
            // Cek jika editor aktif, panggil triggerSave()
            if (tinymce.get('body')) {
                tinymce.get('body').save();
            }
            // Kirim form
            document.getElementById('contentForm').submit();
        }

        function toggleContentTypeFields() {
            const type = document.getElementById('type').value;
            const bodyField = document.getElementById('body_field');
            const fileUploadField = document.getElementById('file_upload_field');
            const quizFormFieldsContainer = document.getElementById('quiz_form_fields');
            const bodyInput = document.getElementById('body');
            const fileInput = document.getElementById('file_upload');

            if (tinymce.get('body')) {
                tinymce.get('body').destroy();
            }

            bodyField.style.display = 'none';
            fileUploadField.style.display = 'none';
            quizFormFieldsContainer.style.display = 'none';

            bodyInput.removeAttribute('required');
            fileInput.removeAttribute('required');

            if (type === 'text' || type === 'video' || type === 'essay') {
                bodyField.style.display = 'block';
                bodyInput.setAttribute('required', 'required');

                if (type === 'text' || type === 'essay') {
                    tinymce.init({
                        selector: 'textarea#body',
                        plugins: 'code table lists link image media autosave wordcount fullscreen template',
                        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link image media',
                        branding: false,
                    });
                }
            } else if (type === 'document' || type === 'image') {
                fileUploadField.style.display = 'block';
                fileInput.setAttribute('required', 'required');
            } else if (type === 'quiz') {
                quizFormFieldsContainer.style.display = 'block';
                // Anda mungkin perlu memanggil fungsi untuk memuat form kuis di sini jika ada
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleContentTypeFields();
        });
    </script>
</x-app-layout>