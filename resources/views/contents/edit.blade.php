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
                    <form method="POST" action="{{ route('lessons.contents.update', [$lesson, $content]) }}" enctype="multipart/form-data">
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
                                {{-- <option value="quiz">Kuis (Nanti)</option> --}}
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toggleContentTypeFields();
        });

        function toggleContentTypeFields() {
            const type = document.getElementById('type').value;
            const bodyField = document.getElementById('body_field');
            const fileUploadField = document.getElementById('file_upload_field');
            const bodyInput = document.getElementById('body');
            const fileInput = document.getElementById('file_upload');

            // Reset required state
            bodyInput.removeAttribute('required');
            fileInput.removeAttribute('required');

            // Hide all initially
            bodyField.classList.add('hidden');
            fileUploadField.classList.add('hidden');

            if (type === 'text' || type === 'video') {
                bodyField.classList.remove('hidden');
                bodyInput.setAttribute('required', 'required');
            } else if (type === 'document' || type === 'image') {
                fileUploadField.classList.remove('hidden');
                // Jika ada file lama, file input tidak wajib. Jika tidak ada, wajib.
                if (!'{{ $content->file_path }}') { // Cek apakah ada file_path lama
                    fileInput.setAttribute('required', 'required');
                }
            }
        }
    </script>
</x-app-layout>