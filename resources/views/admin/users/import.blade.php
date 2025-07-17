<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Unggah Pengguna Massal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg" role="alert">
                        <h3 class="font-bold text-lg mb-2">Petunjuk</h3>
                        <p class="text-sm">
                            Gunakan fitur ini untuk mendaftarkan banyak pengguna sekaligus dari file Excel atau CSV.
                        </p>
                        <ul class="list-disc list-inside mt-2 text-sm space-y-1">
                            <li>Pastikan file Anda memiliki 3 kolom dengan urutan: <strong>Nama, Email, Password</strong>.</li>
                            <li>Baris pertama akan diabaikan (dianggap sebagai header).</li>
                            <li>Email harus unik dan belum terdaftar di sistem.</li>
                            <li>Password minimal 8 karakter.</li>
                            <li>Semua pengguna yang berhasil diimpor akan otomatis diberi peran "Participant".</li>
                        </ul>
                        <div class="mt-4">
                            <a href="{{ route('admin.users.import.template') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh Template CSV
                            </a>
                        </div>
                    </div>

                    @if (session('import_errors'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                            <strong class="font-bold">Beberapa baris gagal diimpor:</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Course Selection -->
                        <div class="mb-4">
                            <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Daftarkan ke Kursus:</label>
                            <select name="course_id" id="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label for="user_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Pengguna (.xlsx, .xls, .csv):</label>
                            <input type="file" name="user_file" id="user_file" required class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            "/>
                             @error('user_file')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Impor Pengguna') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
