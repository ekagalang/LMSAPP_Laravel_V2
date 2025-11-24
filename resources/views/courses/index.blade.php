<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 -mx-4 -my-2 px-4 py-8 sm:px-6 lg:px-8 rounded-2xl shadow-lg">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-2 flex items-center">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Manajemen Kursus
                    </h2>
                    <p class="text-indigo-100 text-lg">Kelola dan pantau semua kursus pembelajaran Anda</p>
                </div>
                @can('create', App\Models\Course::class)
                    <a href="{{ route('courses.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 border border-transparent rounded-xl font-semibold text-sm uppercase tracking-wider hover:bg-indigo-50 hover:scale-105 focus:bg-indigo-50 active:bg-indigo-100 focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Kursus Baru
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Alert -->
            @if (session('success'))
                <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 rounded-r-xl shadow-md" role="alert">
                    <div class="flex items-center p-6">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-green-800">Berhasil!</h3>
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Error Alert -->
            @if (session('error'))
                <div class="mb-8 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 rounded-r-xl shadow-md" role="alert">
                    <div class="flex items-center p-6">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-red-800">Error!</h3>
                            <p class="text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl border border-gray-100">
                <div class="p-8">
                    @if ($courses->isEmpty())
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div class="mx-auto w-32 h-32 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mb-8">
                                <svg class="w-16 h-16 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Belum Ada Kursus</h3>
                            <p class="text-lg text-gray-500 mb-8 max-w-md mx-auto">Mulai perjalanan pembelajaran dengan membuat kursus pertama Anda!</p>
                            @can('create', App\Models\Course::class)
                                <a href="{{ route('courses.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-2xl hover:from-indigo-700 hover:to-purple-700 focus:ring-4 focus:ring-indigo-300 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Buat Kursus Pertama
                                </a>
                            @endcan
                        </div>
                    @else
                        <!-- Courses Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-8">
                            @foreach ($courses as $course)
                                <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-gray-100 overflow-hidden transition-all duration-500 hover:scale-[1.02] hover:border-indigo-200">
                                    <!-- Course Image -->
                                    <div class="relative overflow-hidden">
                                        @if ($course->thumbnail)
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                                 alt="{{ $course->title }}" 
                                                 class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-700">
                                        @else
                                            <div class="w-full h-56 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                                                <div class="text-center">
                                                    <svg class="w-16 h-16 text-indigo-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-indigo-400 font-medium">Tidak Ada Gambar</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Status Badge -->
                                        <div class="absolute top-4 right-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg
                                                {{ $course->status === 'published' 
                                                    ? 'bg-green-100 text-green-800 border border-green-200' 
                                                    : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                                @if($course->status === 'published')
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Published
                                                @else
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Draft
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Course Content -->
                                    <div class="p-6">
                                        <div class="mb-4">
                                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition-colors duration-300 line-clamp-2">
                                                {{ $course->title }}
                                            </h3>
                                            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4">
                                                {{ Str::limit($course->description, 120) }}
                                            </p>
                                        </div>

                                        <!-- Instructor Info -->
                                        <div class="flex items-center mb-6 p-3 bg-gray-50 rounded-xl">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Instruktur</p>
                                                <p class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ $course->instructors->pluck('name')->join(', ') ?: 'Belum ditentukan' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex flex-wrap gap-2">
                                            <!-- View Button -->
                                            <a href="{{ route('courses.show', $course) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all duration-200 shadow-md hover:shadow-lg">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat
                                            </a>

                                            <!-- Edit Button -->
                                            @can('update', $course)
                                                <a href="{{ route('courses.edit', $course) }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-200 transition-all duration-200 shadow-md hover:shadow-lg">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            @endcan

                                            <!-- Duplicate Button -->
                                            @can('duplicate', App\Models\Course::class)
                                                <form action="{{ route('courses.duplicate', $course) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menduplikasi kursus ini?');">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200 transition-all duration-200 shadow-md hover:shadow-lg">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Duplikat
                                                    </button>
                                                </form>
                                            @endcan

                                            <!-- Delete Button -->
                                            @can('delete', $course)
                                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini? Tindakan ini tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-200 transition-all duration-200 shadow-md hover:shadow-lg">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-12">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
        }
    </style>
</x-app-layout>