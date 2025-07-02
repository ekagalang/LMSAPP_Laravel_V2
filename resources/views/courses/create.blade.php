<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('courses.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('Kembali ke Daftar Kursus') }}
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Kursus Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Judul Kursus -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Kursus</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('title') }}" required>
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thumbnail -->
                        <div class="mb-4">
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700">Gambar Sampul (Thumbnail)</label>
                            <input type="file" name="thumbnail" id="thumbnail" class="mt-1 block w-full">
                            @error('thumbnail')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Kursus -->
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status Kursus</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                {{-- 
                                    PERBAIKAN: 
                                    Gunakan `$course->status ?? 'draft'`
                                    Ini akan menggunakan nilai default 'draft' jika $course tidak ada (di halaman create),
                                    dan akan menggunakan $course->status jika ada (di halaman edit).
                                --}}
                                <option value="draft" @selected(old('status', isset($course) ? $course->status : 'draft') == 'draft')>Draft</option>
                                <option value="published" @selected(old('status', isset($course) ? $course->status : 'published') == 'published')>Published</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('courses.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Kursus') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
