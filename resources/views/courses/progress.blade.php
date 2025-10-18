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
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 2 Column Layout: Kiri = Daftar Peserta, Kanan = Analytics -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">

                <!-- KOLOM KIRI: Daftar Peserta (8 kolom / 66%) -->
                <div class="xl:col-span-8 space-y-4 min-w-0">
                    <!-- Stats Cards Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-lg bg-white bg-opacity-20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-blue-100 text-xs">Total Peserta</p>
                                    <p class="text-xl font-bold">{{ $enrolledUsers->total() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-5 text-white shadow-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-lg bg-white bg-opacity-20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-green-100 text-xs">Selesai (hal. ini)</p>
                                    <p class="text-xl font-bold">{{ collect($participantsProgress)->where('progress_percentage', 100)->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-lg bg-white bg-opacity-20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-purple-100 text-xs">Total Materi</p>
                                    <p class="text-xl font-bold">{{ $totalContentCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Card with Scrollable List -->
                    <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden" style="max-height: calc(100vh - 260px);">
                        <!-- Fixed Header - Filter and Search -->
                        <div class="p-4 bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                                </svg>
                                Filter & Pencarian
                            </h3>

                            <div class="grid lg:grid-cols-2 gap-4">
                                <!-- Course Filter -->
                                <div>
                                    <label for="course_filter" class="block text-sm font-semibold text-gray-700 mb-2">
                                        🔄 Pindah ke Kursus Lain
                                    </label>
                                    <select id="course_filter" onchange="if(this.value) window.location.href = this.value" class="block w-full pl-4 pr-10 py-2.5 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm bg-white hover:border-gray-400 transition duration-150">
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
                                            🔍 Cari Peserta
                                        </label>
                                        <div class="flex gap-2">
                                            <div class="relative flex-1">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                                <input type="text" name="search" id="search" class="pl-9 block w-full py-2.5 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nama atau email..." value="{{ request('search') }}" />
                                            </div>
                                            <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transition">
                                                Cari
                                            </button>
                                            @if(request('search'))
                                                <a href="{{ route('courses.progress', $course) }}" class="px-3 py-2.5 text-sm text-gray-600 hover:text-gray-900 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                                    Reset
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Scrollable Table -->
                        <div class="overflow-y-auto overflow-x-auto" style="max-height: calc(100vh - 450px);">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            👤 Peserta
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            📈 Progress
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            📍 Posisi
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            ⚙️ Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse ($participantsProgress as $participant)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                                            <span class="text-white font-bold">
                                                                {{ strtoupper(substr($participant['name'], 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-bold text-gray-900">{{ $participant['name'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $participant['email'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="space-y-1">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="font-medium text-gray-700">{{ $participant['completed_count'] }}/{{ $totalContentCount }}</span>
                                                        <span class="font-bold text-gray-900">{{ $participant['progress_percentage'] }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $participant['progress_percentage'] }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-xs text-gray-900 font-medium">{{ Str::limit($participant['last_position'], 30) }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('courses.participant.progress', ['course' => $course, 'user' => $participant['id']]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition shadow-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <h3 class="text-base font-medium text-gray-900 mb-1">Tidak ada data</h3>
                                                    <p class="text-sm text-gray-500">Tidak ada peserta yang cocok dengan kriteria pencarian.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($enrolledUsers->hasPages())
                            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                                    <div class="text-xs text-gray-700">
                                        Menampilkan <span class="font-medium">{{ $enrolledUsers->firstItem() }}</span>
                                        sampai <span class="font-medium">{{ $enrolledUsers->lastItem() }}</span>
                                        dari <span class="font-medium">{{ $enrolledUsers->total() }}</span> peserta
                                    </div>
                                    <div>
                                        {{ $enrolledUsers->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- KOLOM KANAN: Analytics (4 kolom / 33%) -->
                <div class="xl:col-span-4 min-w-0">
                    <!-- Sticky Analytics Card -->
                    <div class="sticky top-6">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" style="max-height: calc(100vh - 100px);">
                            <!-- Header -->
                            <div class="p-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                                <h3 class="text-lg font-bold flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Analytics & Insights
                                </h3>
                                <p class="text-indigo-100 text-xs mt-1">Data dari {{ $analytics['total_participants'] }} peserta</p>
                            </div>

                            <!-- Scrollable Content -->
                            <div class="overflow-y-auto p-4 space-y-4" style="max-height: calc(100vh - 180px);">
                                <!-- Progress Distribution -->
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4">
                                    <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Distribusi Progress</h4>
                                    <div class="space-y-2">
                                        @php
                                            $distributionColors = [
                                                '0-25' => ['bg' => 'bg-red-500', 'text' => 'text-red-700'],
                                                '26-50' => ['bg' => 'bg-orange-500', 'text' => 'text-orange-700'],
                                                '51-75' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-700'],
                                                '76-99' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-700'],
                                                '100' => ['bg' => 'bg-green-500', 'text' => 'text-green-700'],
                                            ];
                                            $labels = [
                                                '0-25' => '0-25%',
                                                '26-50' => '26-50%',
                                                '51-75' => '51-75%',
                                                '76-99' => '76-99%',
                                                '100' => '100%',
                                            ];
                                        @endphp

                                        @foreach($analytics['distribution'] as $range => $count)
                                            <div>
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="font-medium text-gray-700">{{ $labels[$range] }}</span>
                                                    <span class="font-bold {{ $distributionColors[$range]['text'] }}">{{ $count }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    @php
                                                        $percentage = $analytics['total_participants'] > 0 ? ($count / $analytics['total_participants']) * 100 : 0;
                                                    @endphp
                                                    <div class="{{ $distributionColors[$range]['bg'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Average -->
                                    <div class="mt-4 pt-4 border-t border-gray-300">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-700">Rata-rata:</span>
                                            <div class="text-xl font-bold text-indigo-600">{{ $analytics['average_progress'] }}%</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Lessons -->
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg p-4">
                                    <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Top Posisi Terakhir
                                    </h4>

                                    @if(count($analytics['top_lessons']) > 0)
                                        <div class="space-y-2">
                                            @foreach($analytics['top_lessons'] as $lessonId => $lesson)
                                                <div class="bg-white rounded-lg p-3 shadow-sm">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <div class="text-xs font-bold text-gray-900 mb-1">{{ $lesson['title'] }}</div>
                                                            <div class="text-xs text-gray-500">{{ $lesson['count'] }} peserta</div>
                                                        </div>
                                                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">#{{ $loop->iteration }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-6 text-gray-500">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-xs">Belum ada data</p>
                                        </div>
                                    @endif

                                    <!-- Quick Stats -->
                                    <div class="mt-4 pt-4 border-t border-indigo-200">
                                        <div class="grid grid-cols-2 gap-3 text-center">
                                            <div class="bg-white rounded-lg p-2">
                                                <div class="text-lg font-bold text-green-600">{{ $analytics['completed_participants'] }}</div>
                                                <div class="text-xs text-gray-600">Selesai</div>
                                            </div>
                                            <div class="bg-white rounded-lg p-2">
                                                <div class="text-lg font-bold text-blue-600">{{ $analytics['in_progress_participants'] }}</div>
                                                <div class="text-xs text-gray-600">Progress</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
