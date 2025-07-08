<x-app-layout>
    {{-- PERBAIKAN: Tambahkan Alpine.js untuk state management sidebar --}}
    <div x-data="{ sidebarOpen: true }" class="flex flex-col md:flex-row min-h-screen">
        <aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="w-full md:w-80 bg-gray-50 border-r p-4 h-full md:h-screen md:sticky top-0 flex-shrink-0">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 truncate">{{ $course->title }}</h3>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <nav class="overflow-y-auto h-[calc(100vh-5rem)]">
                <ul>
                    @foreach ($course->lessons->sortBy('id') as $lesson)
                        <li class="mb-3">
                            <div class="flex items-center">
                                @if (Auth::user()->lessons->contains($lesson->id))
                                    <span class="text-green-500 mr-2">&#10004;</span>
                                @endif
                                <strong class="text-gray-900">{{ $lesson->title }}</strong>
                            </div>
                            <ul class="ml-4 mt-2 space-y-1 border-l-2 border-gray-200">
                                @foreach ($lesson->contents->sortBy('id') as $c)
                                    <li class="relative">
                                        <a href="{{ route('contents.show', $c) }}"
                                           class="block pl-4 pr-2 py-1.5 text-sm transition-colors duration-200
                                                @if ($c->id === $content->id) text-blue-600 font-bold bg-blue-100 border-l-4 border-blue-600 -ml-1 @else text-gray-600 hover:text-gray-900 hover:bg-gray-200 border-l-4 border-transparent @endif">
                                            <div class="flex items-center justify-between">
                                                <span>{{ $c->title }}</span>
                                                @if (Auth::user()->contents->contains($c->id))
                                                    <span class="text-green-500">&#10003;</span>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <main class="w-full p-4 md:p-8 transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                {{-- PERBAIKAN: Tombol untuk toggle sidebar --}}
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md bg-gray-200 hover:bg-gray-300">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                {{-- PERBAIKAN: Tombol kembali ke kursus --}}
                <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                    &larr; Kembali ke Kursus
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-3xl font-bold mb-4 text-gray-900">{{ $content->title }}</h2>

                    {{-- ✅ PERBAIKAN DI SINI --}}
                    @if($content->type == 'video')
                        <div class="aspect-w-16 aspect-h-9"><iframe src="https://www.youtube.com/embed/{{ $content->video_url }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full rounded-lg"></iframe></div>
                    @elseif($content->type == 'file')
                        <a href="{{ Storage::url($content->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Download File</a>
                    @elseif($content->type == 'text' || $content->type == 'essay')
                        {{-- Menampilkan isi pertanyaan untuk Teks dan Esai --}}
                        <div class="prose max-w-none">{!! $content->body !!}</div>
                    @endif

                    @include('contents.partials.essay-quiz-section')
                </div>
            </div>

            <div class="flex justify-between mt-8">
                @if ($previousContent)
                    <a href="{{ route('contents.show', $previousContent) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        &larr; Materi Sebelumnya
                    </a>
                @else
                    <div></div>
                @endif

                @if ($nextContent)
                    <a href="{{ route('contents.show', $nextContent) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                        Materi Selanjutnya &rarr;
                    </a>
                @else
                    <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                        Selesai
                    </a>
                @endif
            </div>
        </main>
    </div>
</x-app-layout>