<x-app-layout>
    <div x-data="{ sidebarOpen: true }" class="flex flex-col md:flex-row min-h-screen bg-gray-100">

        <aside 
            x-show="sidebarOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="w-full md:w-80 bg-white border-r p-4 h-full md:h-screen md:sticky top-0 flex-shrink-0 z-20">
            
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 truncate">{{ $course->title }}</h3>
                {{-- Tombol tutup sidebar untuk tampilan mobile --}}
                <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            {{-- Daftar pelajaran dan konten dibuat scrollable --}}
            <nav class="overflow-y-auto h-[calc(100vh-5rem)]">
                <ul>
                    @foreach ($course->lessons->sortBy('order') as $lesson)
                        <li class="mb-3">
                            <strong class="text-gray-900 px-2">{{ $loop->iteration }}. {{ $lesson->title }}</strong>
                            <ul class="ml-2 mt-2 space-y-1 border-l-2 border-gray-200">
                                @foreach ($lesson->contents->sortBy('order') as $c)
                                    <li class="relative">
                                        {{-- Navigasi antar konten --}}
                                        <a href="{{ route('contents.show', $c) }}"
                                           class="block pl-4 pr-2 py-2 text-sm transition-colors duration-200 rounded-r-md
                                                {{-- Style untuk konten yang sedang aktif --}}
                                                @if ($c->id === $content->id) 
                                                    text-indigo-700 font-bold bg-indigo-100 border-l-4 border-indigo-600 -ml-0.5 
                                                @else 
                                                    text-gray-600 hover:text-gray-900 hover:bg-gray-100 border-l-4 border-transparent 
                                                @endif">
                                            
                                            <div class="flex items-center justify-between">
                                                <span>{{ $c->title }}</span>
                                                {{-- Tanda centang jika sudah selesai --}}
                                                @if (Auth::user()->completedContents->contains($c->id))
                                                    <span class="text-green-500">&#10004;</span>
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

        <main class="w-full p-4 md:p-8 flex-1">
            <div class="max-w-4xl mx-auto">
                {{-- Tombol untuk membuka sidebar & kembali ke kursus --}}
                <div class="flex items-center justify-between mb-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md bg-white hover:bg-gray-200 shadow-sm">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                        &larr; Kembali ke Detail Kursus
                    </a>
                </div>

                {{-- Card untuk isi konten --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-24"> {{-- beri margin bottom agar tidak tertutup nav bawah --}}
                    <div class="p-6 md:p-8">
                        <h2 class="text-3xl font-bold mb-2 text-gray-900">{{ $content->title }}</h2>
                        
                        @if($content->description)
                            <p class="text-md text-gray-600 mb-6 pb-6 border-b">{{ $content->description }}</p>
                        @endif

                        {{-- Tampilkan isi konten berdasarkan tipenya --}}
                        @if($content->type == 'video' && $content->body)
                            <div class="aspect-video rounded-lg overflow-hidden shadow-lg mb-6">
                                <iframe class="w-full h-full" src="{{ str_replace('watch?v=', 'embed/', $content->body) }}?autoplay=1&amp;modestbranding=1&amp;rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @elseif(in_array($content->type, ['document', 'image']))
                            <div class="my-6">
                                 @if($content->type == 'image')
                                    <img src="{{ Storage::url($content->file_path) }}" alt="{{ $content->title }}" class="max-w-full h-auto rounded-lg shadow-md">
                                 @else
                                    <a href="{{ Storage::url($content->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Download Dokumen: {{ basename($content->file_path) }}</a>
                                 @endif
                            </div>
                        @elseif(in_array($content->type, ['text', 'essay']))
                            <div class="prose max-w-none mt-4">{!! $content->body !!}</div>
                        @endif

                        {{-- Bagian untuk form Esai atau tombol Kuis --}}
                        @include('contents.partials.essay-quiz-section')
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                    <div class="p-6 md:p-8">
                        @include('contents.partials.discussion-section')
                    </div>
                </div>
            </div>
        </main>

        {{-- Logika untuk menentukan konten sebelumnya dan selanjutnya --}}
        @php
            $allContents = $course->lessons->sortBy('order')->flatMap->contents->sortBy('order')->values();
            $currentIndex = $allContents->search(fn($item) => $item->id === $content->id);
            $previousContent = $currentIndex > 0 ? $allContents[$currentIndex - 1] : null;
            $nextContent = ($currentIndex !== false && $currentIndex < $allContents->count() - 1) ? $allContents[$currentIndex + 1] : null;
        @endphp

        <footer class="fixed bottom-0 left-0 w-full bg-white border-t p-4 z-10 shadow-lg-top">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                {{-- Tombol Kembali --}}
                <div>
                    @if ($previousContent)
                        <a href="{{ route('contents.show', $previousContent) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            &larr; Sebelumnya
                        </a>
                    @endif
                </div>

                {{-- Tombol Lanjut / Selesai --}}
                <div>
                    @if ($nextContent)
                        <a href="{{ route('contents.show', $nextContent) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                            Selanjutnya &rarr;
                        </a>
                    @else
                        {{-- Jika ini konten terakhir, tombol berubah jadi Selesai --}}
                        <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                            Selesai Kursus
                        </a>
                    @endif
                </div>
            </div>
        </footer>
    </div>
</x-app-layout>