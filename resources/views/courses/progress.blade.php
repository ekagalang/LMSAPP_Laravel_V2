<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    📊 Progres Peserta
                </h2>
                <p class="text-gray-600 mt-1">{{ $course->title }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('courses.progress.pdf', $course) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Unduh PDF
                </a>
                <a href="{{ route('eo.courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-white bg-opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-blue-100">Total Peserta</p>
                            <p class="text-2xl font-bold">{{ $enrolledUsers->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-white bg-opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-green-100">Selesai 100% (hal. ini)</p>
                            <p class="text-2xl font-bold">{{ collect($participantsProgress)->where('progress_percentage', 100)->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-white bg-opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-purple-100">Total Materi</p>
                            <p class="text-2xl font-bold">{{ $totalContentCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-200">
                <div class="p-6 lg:p-8">
                    <!-- Filter and Search Section -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                            </svg>
                            Filter & Pencarian
                        </h3>
                        
                        <div class="grid lg:grid-cols-2 gap-6">
                            <!-- Course Filter -->
                            <div>
                                <label for="course_filter" class="block text-sm font-semibold text-gray-700 mb-2">
                                    🔄 Pindah ke Kursus Lain
                                </label>
                                <select id="course_filter" onchange="if(this.value) window.location.href = this.value" class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm bg-white hover:border-gray-400 transition duration-150">
                                    @foreach ($instructorCourses as $filterCourse)
                                        <option value="{{ route('courses.progress', $filterCourse) }}" @selected($filterCourse->id == $course->id)>
                                            {{ $filterCourse->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Search Form -->
                            <form action="{{ route('courses.progress', $course) }}" method="GET">
                                <div>
                                    <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                                        🔍 Cari Peserta di Kursus Ini
                                    </label>
                                    <div class="flex">
                                        <div class="relative flex-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>
                                            <x-text-input type="text" name="search" id="search" class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nama atau email peserta..." value="{{ request('search') }}" />
                                        </div>
                                        <x-primary-button class="ml-3 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 rounded-lg shadow-lg hover:shadow-xl transition duration-150">
                                            Cari
                                        </x-primary-button>
                                        @if(request('search'))
                                            <a href="{{ route('courses.progress', $course) }}" class="ml-3 self-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 transition duration-150">
                                                Reset
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            👤 Peserta
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            📈 Progres Penyelesaian
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            📍 Posisi Terakhir
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            ⚙️ Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse ($participantsProgress as $participant)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="px-6 py-5 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-12 w-12">
                                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                                            <span class="text-white font-bold text-lg">
                                                                {{ strtoupper(substr($participant['name'], 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-bold text-gray-900">{{ $participant['name'] }}</div>
                                                        <div class="text-sm text-gray-500 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                            {{ $participant['email'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 whitespace-nowrap min-w-[280px]">
                                                <div class="space-y-2">
                                                    <div class="flex justify-between text-sm">
                                                        <span class="font-medium text-gray-700">
                                                            {{ $participant['completed_count'] }} dari {{ $totalContentCount }} materi
                                                        </span>
                                                        <span class="font-bold text-gray-900">
                                                            {{ $participant['progress_percentage'] }}%
                                                        </span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
                                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full text-xs font-medium text-white text-center leading-none shadow-lg transition-all duration-500 ease-out" style="width: {{ $participant['progress_percentage'] }}%">
                                                        </div>
                                                    </div>
                                                    @if($participant['progress_percentage'] == 100)
                                                        <div class="flex items-center text-green-600 text-xs font-medium">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Selesai!
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 font-medium">{{ $participant['last_position'] }}</div>
                                            </td>
                                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('courses.participant.progress', ['course' => $course, 'user' => $participant['id']]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 shadow-sm hover:shadow-md">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                    Lihat Rincian
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data peserta</h3>
                                                    <p class="text-sm text-gray-500">Tidak ada peserta yang cocok dengan kriteria pencarian Anda.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ✅ PAGINATION -->
                    @if($enrolledUsers->hasPages())
                        <div class="mt-6 px-4 py-3 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="text-sm text-gray-700">
                                    Menampilkan <span class="font-medium">{{ $enrolledUsers->firstItem() }}</span>
                                    sampai <span class="font-medium">{{ $enrolledUsers->lastItem() }}</span>
                                    dari <span class="font-medium">{{ $enrolledUsers->total() }}</span> peserta
                                </div>

                                <div class="flex items-center gap-2">
                                    {{ $enrolledUsers->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>