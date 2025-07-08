<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Rincian Progres: {{ $participant->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Kursus: {{ $course->title }}
                </p>
            </div>
            <a href="{{ route('courses.progress', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                &larr; Kembali ke Ringkasan Progres
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    {{-- Ulangi untuk setiap pelajaran --}}
                    @forelse ($lessons as $lesson)
                        <div class="p-4 border rounded-lg bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">{{ $loop->iteration }}. {{ $lesson->title }}</h3>
                            <ul class="space-y-2">
                                {{-- Ulangi untuk setiap materi di dalam pelajaran --}}
                                @forelse ($lesson->contents as $content)
                                    <li class="flex items-center justify-between p-3 rounded-md {{ $completedContentsMap->has($content->id) ? 'bg-green-50' : 'bg-red-50' }}">
                                        <span class="text-sm font-medium text-gray-700">{{ $content->title }}</span>
                                        
                                        {{-- Tampilkan status Selesai atau Belum --}}
                                        @if ($completedContentsMap->has($content->id))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                &#10004; Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                &#10008; Belum Selesai
                                            </span>
                                        @endif
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500">Tidak ada materi dalam pelajaran ini.</li>
                                @endforelse
                            </ul>
                        </div>
                    @empty
                        <p class="text-center text-gray-500">Kursus ini belum memiliki pelajaran.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>