<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Peserta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Kursus yang Anda Ikuti</h3>

                    @if (Auth::user()->enrolledCourses->isEmpty())
                        <p class="text-center text-gray-500">Anda belum terdaftar di kursus manapun.</p>
                        <p class="text-center mt-4">Jelajahi <a href="{{ route('welcome') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">kursus yang tersedia</a>.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach (Auth::user()->enrolledCourses as $course)
                                @php
                                    $totalLessons = $course->lessons->count();
                                    $completedLessonsCount = Auth::user()->completedLessons->whereIn('id', $course->lessons->pluck('id'))->count();
                                    $progress = ($totalLessons > 0) ? round(($completedLessonsCount / $totalLessons) * 100) : 0;
                                @endphp
                                <div class="bg-gray-50 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                    @if ($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                                            Tidak Ada Gambar
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $course->title }}</h3>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                                        <div class="mt-4">
                                            <div class="flex justify-between mb-1 text-sm font-medium text-gray-700">
                                                <span>Progres: {{ $progress }}%</span>
                                                <span>{{ $completedLessonsCount }} dari {{ $totalLessons }} pelajaran selesai</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-end mt-4">
                                            <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Lanjutkan Kursus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>