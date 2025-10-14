<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                📊 Pemantauan Progres Kursus
            </h2>
            <div class="text-sm text-gray-600">
                Total: {{ $courses->total() }} Kursus
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white bg-opacity-30 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-blue-100 text-sm">Total Kursus</p>
                            <p class="text-2xl font-bold">{{ $courses->total() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white bg-opacity-30 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-green-100 text-sm">Aktif</p>
                            <p class="text-2xl font-bold">{{ $courses->total() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white bg-opacity-30 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-purple-100 text-sm">Rata-rata Progres</p>
                            {{-- PERBAIKAN: Gunakan variabel dari controller --}}
                            <p class="text-2xl font-bold">{{ $overallAverageProgress }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Cards -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Kursus</h3>
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            <span>Grid View</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($courses as $course)
                            <div class="group bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 transform hover:-translate-y-1">
                                <!-- Course Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                            <span class="text-xs font-medium text-green-600 uppercase tracking-wide">Aktif</span>
                                        </div>
                                        <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                                            {{ $course->title }}
                                        </h3>
                                    </div>
                                    <div class="ml-2">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Instructor Info -->
                                <div class="flex items-center mb-4">
                                    <div class="flex -space-x-2">
                                        @foreach($course->instructors->take(3) as $instructor)
                                            <div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center text-white text-xs font-semibold border-2 border-white">
                                                {{ strtoupper(substr($instructor->name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($course->instructors->count() > 3)
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-xs font-semibold border-2 border-white">
                                                +{{ $course->instructors->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-700">Instruktur</p>
                                        <p class="text-xs text-gray-500">{{ $course->instructors->pluck('name')->take(2)->join(', ') }}{{ $course->instructors->count() > 2 ? ' & lainnya' : '' }}</p>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Progres Keseluruhan</span>
                                        {{-- PERBAIKAN: Gunakan data progres rata-rata per kursus --}}
                                        <span class="font-semibold text-gray-900">{{ $course->average_progress ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-500" style="width: {{ $course->average_progress ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="text-center">
                                        {{-- PERBAIKAN: Gunakan data dari withCount --}}
                                        <p class="text-2xl font-bold text-gray-900">{{ $course->enrolled_users_count }}</p>
                                        <p class="text-xs text-gray-500">Siswa Aktif</p>
                                    </div>
                                    <div class="text-center">
                                        {{-- PERBAIKAN: Gunakan data dari withCount --}}
                                        <p class="text-2xl font-bold text-gray-900">{{ $course->lessons_count }}</p>
                                        <p class="text-xs text-gray-500">Pelajaran</p>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="pt-4 border-t border-gray-100">
                                    <a href="{{ route('courses.progress', $course) }}" 
                                       class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition-all duration-200 transform hover:scale-105 group-hover:shadow-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-6a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Lihat Detail Progres
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full">
                                <div class="text-center py-12">
                                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Kursus</h3>
                                    <p class="text-gray-500 mb-6">Belum ada kursus yang dipublikasikan untuk dipantau.</p>
                                    <button class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Tambah Kursus Baru
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination Links -->
                    @if($courses->hasPages())
                        <div class="mt-8 flex justify-center">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>