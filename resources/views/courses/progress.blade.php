<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Progres Peserta: {{ $course->title }}
                </h2>
            </div>
            <div>
                <a href="{{ route('courses.progress.pdf', $course) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Unduh PDF
                </a>
                <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    &larr; Kembali ke Kursus
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- ✅ FORM FILTER DAN PENCARIAN BARU --}}
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        {{-- Filter Kursus --}}
                        <div>
                            <label for="course_filter" class="block text-sm font-medium text-gray-700">Pindah ke Kursus Lain</label>
                            <select id="course_filter" onchange="if(this.value) window.location.href = this.value" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach ($instructorCourses as $filterCourse)
                                    <option value="{{ route('courses.progress', $filterCourse) }}" @selected($filterCourse->id == $course->id)>
                                        {{ $filterCourse->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Pencarian Peserta --}}
                        <form action="{{ route('courses.progress', $course) }}" method="GET">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Cari Peserta di Kursus Ini</label>
                                <div class="mt-1 flex">
                                    <x-text-input type="text" name="search" id="search" class="w-full" placeholder="Nama atau email..." value="{{ request('search') }}" />
                                    <x-primary-button class="ml-2">Cari</x-primary-button>
                                    @if(request('search'))
                                        <a href="{{ route('courses.progress', $course) }}" class="ml-2 self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            {{-- ... (Isi tabel tetap sama seperti sebelumnya) ... --}}
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progres Penyelesaian</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi Terakhir</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($participantsProgress as $participant)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $participant['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $participant['email'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap min-w-[200px]">
                                            <div class="w-full bg-gray-200 rounded-full h-4 mr-3">
                                                <div class="bg-blue-600 h-4 rounded-full text-xs font-medium text-blue-100 text-center p-0.5 leading-none" style="width: {{ $participant['progress_percentage'] }}%">
                                                    {{ $participant['progress_percentage'] }}%
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ $participant['completed_count'] }} dari {{ $totalContentCount }} materi selesai
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $participant['last_position'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('courses.participant.progress', ['course' => $course, 'user' => $participant['id']]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                Lihat Rincian
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada peserta yang cocok dengan kriteria Anda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>