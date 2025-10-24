<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white -mx-6 -mt-6 mb-6 px-6 py-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 p-3 rounded-lg">
                        <i class="fas fa-clipboard-list text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold">Manajemen Kuis</h2>
                        <p class="text-indigo-100 mt-1">Kelola dan monitor semua kuis Anda</p>
                    </div>
                </div>
                @can('manage-courses')
                    <a href="{{ route('quizzes.create') }}"
                       class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:bg-gray-50 transition-all duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Buat Kuis Baru</span>
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <!-- Custom Styles -->
    <style>
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .badge-published {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        }
        .badge-draft {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .btn-action {
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            transform: scale(1.05);
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <div>
                            <p class="font-medium text-green-800">Berhasil!</p>
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                        <div>
                            <p class="font-medium text-red-800">Error!</p>
                            <p class="text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stats Overview -->
            @if (!$quizzes->isEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Kuis -->
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                        <div class="flex items-center">
                            <div class="stats-card p-3 rounded-lg">
                                <i class="fas fa-clipboard-list text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Kuis</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $quizzes->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kuis Published -->
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                        <div class="flex items-center">
                            <div class="bg-green-500 p-3 rounded-lg">
                                <i class="fas fa-eye text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Published</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $quizzes->where('status', 'published')->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kuis Draft -->
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                        <div class="flex items-center">
                            <div class="bg-yellow-500 p-3 rounded-lg">
                                <i class="fas fa-edit text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Draft</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $quizzes->where('status', 'draft')->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Soal -->
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                        <div class="flex items-center">
                            <div class="bg-purple-500 p-3 rounded-lg">
                                <i class="fas fa-question-circle text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Soal</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $quizzes->sum(function($quiz) { return $quiz->questions->count(); }) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quiz Cards -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                @if ($quizzes->isEmpty())
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-clipboard-list text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada kuis</h3>
                        <p class="text-gray-500 mb-6">Mulai buat kuis pertama Anda untuk mengukur pemahaman peserta</p>
                        @can('manage-courses')
                            <a href="{{ route('quizzes.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Buat Kuis Pertama
                            </a>
                        @endcan
                    </div>
                @else
                    <!-- Quiz Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Kuis</h3>
                            <div class="text-sm text-gray-500">
                                {{ $quizzes->count() }} kuis ditemukan
                            </div>
                        </div>
                    </div>

                    <!-- Quiz Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($quizzes as $quiz)
                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm card-hover overflow-hidden">
                                    <!-- Quiz Header -->
                                    <div class="p-6 pb-4">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                                    {{ $quiz->title }}
                                                </h4>
                                                <p class="text-sm text-gray-600 mb-3">
                                                    <i class="fas fa-user mr-1"></i>
                                                    Oleh: {{ $quiz->instructor->name }}
                                                </p>
                                                @if($quiz->lesson && $quiz->lesson->course)
                                                    <p class="text-sm text-gray-500">
                                                        <i class="fas fa-book mr-1"></i>
                                                        {{ $quiz->lesson->course->title }}
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="ml-3 px-3 py-1 text-xs font-semibold rounded-full text-white {{ $quiz->status === 'published' ? 'badge-published' : 'badge-draft' }}">
                                                {{ $quiz->status === 'published' ? 'Published' : 'Draft' }}
                                            </span>
                                        </div>

                                        <!-- Quiz Stats -->
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div class="bg-blue-50 p-3 rounded-lg text-center">
                                                <div class="text-lg font-bold text-blue-600">{{ $quiz->questions->count() }}</div>
                                                <div class="text-xs text-blue-500">Soal</div>
                                            </div>
                                            <div class="bg-green-50 p-3 rounded-lg text-center">
                                                <div class="text-lg font-bold text-green-600">{{ $quiz->total_marks }}</div>
                                                <div class="text-xs text-green-500">Total Nilai</div>
                                            </div>
                                        </div>

                                        <!-- Additional Info -->
                                        <div class="space-y-2 text-sm text-gray-600">
                                            <div class="flex items-center justify-between">
                                                <span class="flex items-center">
                                                    <i class="fas fa-target mr-2 text-yellow-500"></i>
                                                    Passing Grade
                                                </span>
                                                <span class="font-medium">{{ $quiz->pass_marks }} poin</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="flex items-center">
                                                    <i class="fas fa-clock mr-2 text-blue-500"></i>
                                                    Batas Waktu
                                                </span>
                                                <span class="font-medium">
                                                    {{ $quiz->time_limit ? $quiz->time_limit . ' menit' : 'Tidak ada' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="bg-gray-50 px-6 py-4">
                                        <div class="flex items-center justify-between space-x-2">
                                            <a href="{{ route('quizzes.show', $quiz) }}" 
                                               class="flex-1 bg-indigo-600 text-white text-center py-2 px-4 rounded-lg font-medium hover:bg-indigo-700 btn-action">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </a>
                                            
                                            @can('update', $quiz)
                                                <a href="{{ route('quizzes.edit', $quiz) }}" 
                                                   class="flex-1 bg-purple-600 text-white text-center py-2 px-4 rounded-lg font-medium hover:bg-purple-700 btn-action">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                            @endcan
                                            
                                            @can('delete', $quiz)
                                                <form action="{{ route('quizzes.destroy', $quiz) }}" method="POST" class="flex-1" 
                                                      onsubmit="return confirm('Yakin ingin menghapus kuis ini? Semua data akan terhapus permanen.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="w-full bg-red-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-red-700 btn-action">
                                                        <i class="fas fa-trash mr-1"></i> Hapus
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            @if (!$quizzes->isEmpty())
                <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @can('manage-courses')
                            <a href="{{ route('quizzes.create') }}" 
                               class="flex items-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-plus text-indigo-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Buat Kuis Baru</h4>
                                    <p class="text-sm text-gray-500">Tambah kuis untuk kursus</p>
                                </div>
                            </a>
                        @endcan
                        
                        <a href="{{ route('courses.index') }}" 
                           class="flex items-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-book text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Kelola Kursus</h4>
                                <p class="text-sm text-gray-500">Atur kursus dan materi</p>
                            </div>
                        </a>
                        
                        <a href="#" onclick="window.print()" 
                           class="flex items-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-download text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Export Data</h4>
                                <p class="text-sm text-gray-500">Unduh laporan kuis</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</x-app-layout>