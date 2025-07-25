<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('courses.index') }}" 
                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium mb-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Kembali ke Daftar Kursus') }}
                </a>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Edit Kursus') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $course->title }}</p>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <div class="flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $course->status === 'published' ? '✅ Published' : '📝 Draft' }}
                </div>
                <div class="text-sm text-gray-500">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editing Mode
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center">
                    <div class="flex items-center text-indigo-600">
                        <div class="flex items-center justify-center w-8 h-8 bg-indigo-600 text-white rounded-full text-sm font-semibold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium">Edit Informasi</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 mx-4"></div>
                    <div class="flex items-center text-gray-400">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-600 rounded-full text-sm">
                            2
                        </div>
                        <span class="ml-2 text-sm">Update Konten</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <!-- Header Form -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Update Kursus</h3>
                    <p class="text-indigo-100 text-sm mt-1">Perbarui informasi kursus Anda</p>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Grid Layout -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Left Column -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Judul Kursus -->
                                <div class="group">
                                    <label for="title" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        Judul Kursus *
                                    </label>
                                    <input type="text" 
                                           name="title" 
                                           id="title" 
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400" 
                                           value="{{ old('title', $course->title) }}" 
                                           required 
                                           autofocus
                                           placeholder="Masukkan judul kursus yang menarik...">
                                    @error('title')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Deskripsi -->
                                <div class="group">
                                    <label for="description" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                        </svg>
                                        Deskripsi
                                    </label>
                                    <textarea name="description" 
                                              id="description" 
                                              rows="5" 
                                              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400 resize-none" 
                                              placeholder="Jelaskan tentang kursus Anda, apa yang akan dipelajari siswa...">{{ old('description', $course->description) }}</textarea>
                                    <div class="flex justify-between items-center mt-1">
                                        @error('description')
                                            <p class="text-red-500 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @else
                                            <p class="text-gray-400 text-xs">Update deskripsi untuk memberikan info lebih jelas</p>
                                        @enderror
                                        <p class="text-gray-400 text-xs" id="char-count">0 karakter</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- ================================================================= -->
                                <!-- PERBAIKAN: Thumbnail Upload dengan Preview -->
                                <!-- ================================================================= -->
                                <div x-data="{ imagePreview: '{{ $course->thumbnail ? Storage::url($course->thumbnail) : '' }}' }" class="group">
                                    <label for="thumbnail" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Gambar Sampul
                                    </label>
                                    
                                    <!-- Image Preview -->
                                    <div x-show="imagePreview" class="mb-4 p-4 bg-gray-50 rounded-lg">
                                        <img :src="imagePreview" alt="Current Thumbnail" class="w-full h-32 object-cover rounded-lg shadow-md mb-3">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="clear_thumbnail" id="clear_thumbnail" 
                                                   @change="if ($event.target.checked) imagePreview = ''"
                                                   class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                            <label for="clear_thumbnail" class="ml-2 text-sm text-red-600 hover:text-red-700 cursor-pointer">
                                                🗑️ Hapus Gambar Saat Ini
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors duration-200">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="thumbnail" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                                    <span>Ganti gambar</span>
                                                    <input id="thumbnail" name="thumbnail" type="file" class="sr-only" accept="image/*" @change="imagePreview = URL.createObjectURL($event.target.files[0]); document.getElementById('clear_thumbnail').checked = false;">
                                                </label>
                                                <p class="pl-1">atau drag & drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                        </div>
                                    </div>
                                    @error('thumbnail')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status Kursus -->
                                <div class="group">
                                    <label for="status" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Status Publikasi
                                    </label>
                                    <select name="status" 
                                            id="status" 
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 group-hover:border-gray-400">
                                        <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>
                                            📝 Draft
                                        </option>
                                        <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>
                                            ✅ Published
                                        </option>
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Course Stats -->
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-lg p-4">
                                    <h4 class="text-indigo-800 font-medium text-sm mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Statistik Kursus
                                    </h4>
                                    <div class="grid grid-cols-2 gap-3 text-center">
                                        <div class="bg-white rounded-lg p-2 shadow-sm">
                                            <div class="text-lg font-bold text-gray-800">{{ $course->created_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-600">Dibuat</div>
                                        </div>
                                        <div class="bg-white rounded-lg p-2 shadow-sm">
                                            <div class="text-lg font-bold text-gray-800">{{ $course->updated_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-600">Diperbarui</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('courses.index') }}" 
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batal
                                </a>
                                
                                <div class="flex space-x-3">
                                    <button type="submit" 
                                            class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ __('Perbarui Kursus') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Character counter for description
        const descriptionTextarea = document.getElementById('description');
        const charCountElement = document.getElementById('char-count');
        
        function updateCharCount() {
            const charCount = descriptionTextarea.value.length;
            charCountElement.textContent = charCount + ' karakter';
        }
        
        // Initialize counter
        updateCharCount();
        
        // Update counter on input
        descriptionTextarea.addEventListener('input', updateCharCount);

        // File upload preview
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('New file selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });

        // Clear thumbnail checkbox behavior
        const clearThumbnailCheckbox = document.getElementById('clear_thumbnail');
        if (clearThumbnailCheckbox) {
            clearThumbnailCheckbox.addEventListener('change', function(e) {
                if (e.target.checked) {
                    console.log('Thumbnail will be cleared on save');
                }
            });
        }
    </script>
</x-app-layout>