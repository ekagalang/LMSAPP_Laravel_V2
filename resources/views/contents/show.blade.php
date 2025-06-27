{{-- resources/views/contents/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konten Pelajaran:') }} {{ $content->title }}
        </h2>
        <p class="text-sm text-gray-600">
            <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-900">{{ $course->title }}</a> >
            {{ $lesson->title }}
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $content->title }}</h3>
                <div class="text-sm text-gray-600 mb-4">Tipe: <span class="capitalize font-medium">{{ $content->type }}</span></div>

                <div class="content-display mt-6">
                    @if ($content->type === 'text')
                        <div class="prose max-w-none">
                            {!! $content->body !!}
                        </div>
                    @elseif ($content->type === 'video')
                        @php
                            $youtubeEmbed = '';
                            $vimeoEmbed = '';
                            if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|)([\w-]{11})/', $content->body, $matches)) {
                                $youtubeEmbed = "https://www.youtube.com/embed/" . $matches[1]; // Perbaiki URL embed YouTube
                            } elseif (preg_match('/(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(?:video\/|channels\/staffpicks\/video\/|)(\d+)/', $content->body, $matches)) {
                                $vimeoEmbed = "https://player.vimeo.com/video/" . $matches[1];
                            }
                        @endphp

                        @if (!empty($youtubeEmbed))
                            <div class="aspect-w-16 aspect-h-9">
                                <iframe class="w-full h-96 rounded-lg shadow-lg" src="{{ $youtubeEmbed }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @elseif (!empty($vimeoEmbed))
                            <div class="aspect-w-16 aspect-h-9">
                                <iframe class="w-full h-96 rounded-lg shadow-lg" src="{{ $vimeoEmbed }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @else
                            <p class="text-red-500">URL Video tidak valid atau tidak didukung.</p>
                            <p class="text-gray-600">URL: {{ $content->body }}</p>
                        @endif
                    @elseif ($content->type === 'document')
                        @if ($content->file_path)
                            <p>Unduh Dokumen: <a href="{{ asset('storage/' . $content->file_path) }}" target="_blank" class="text-indigo-600 hover:underline">{{ basename($content->file_path) }}</a></p>
                        @else
                            <p class="text-red-500">Tidak ada file dokumen yang ditemukan.</p>
                        @endif
                    @elseif ($content->type === 'image')
                        @if ($content->file_path)
                            <img src="{{ asset('storage/' . $content->file_path) }}" alt="{{ $content->title }}" class="max-w-full h-auto rounded-lg shadow-lg">
                        @else
                            <p class="text-red-500">Tidak ada file gambar yang ditemukan.</p>
                        @endif
                    @elseif ($content->type === 'quiz') {{-- Bagian baru untuk menampilkan kuis --}}
                        @if ($content->quiz)
                            <div class="border border-gray-200 rounded-lg p-4 bg-blue-50">
                                <h4 class="text-xl font-bold text-blue-800 mb-2">{{ $content->quiz->title }}</h4>
                                <p class="text-gray-700 mb-3">{{ $content->quiz->description }}</p>
                                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mb-4">
                                    <div><strong>Total Soal:</strong> {{ $content->quiz->questions->count() }}</div>
                                    <div><strong>Total Nilai:</strong> {{ $content->quiz->total_marks }}</div>
                                    <div><strong>Nilai Lulus:</strong> {{ $content->quiz->pass_marks }}</div>
                                    <div><strong>Batas Waktu:</strong> {{ $content->quiz->time_limit ? $content->quiz->time_limit . ' menit' : 'Tidak ada' }}</div>
                                </div>
                                @auth
                                    @if (Auth::user()->isParticipant())
                                        @if ($content->quiz->status == 'published')
                                            <form action="{{ route('quizzes.start_attempt', $content->quiz) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    {{ __('Mulai Kuis') }}
                                                </button>
                                            </form>
                                        @else
                                            <p class="text-red-500">Kuis ini belum dipublikasikan dan tidak dapat dikerjakan.</p>
                                        @endif
                                    @elsecan('update', $course) {{-- Instruktur/Admin bisa melihat dan mengedit dari sini --}}
                                        <div class="flex space-x-2">
                                            <a href="{{ route('lessons.contents.edit', [$lesson, $content]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Edit Kuis (via Konten)
                                            </a>
                                            {{-- Tombol hapus kuis akan mengikuti hapus konten --}}
                                        </div>
                                    @endauth
                                </div>
                            @else
                                <p class="text-red-500">Kuis tidak ditemukan untuk konten ini.</p>
                            @endif
                        @else
                            <p class="text-gray-500">Konten ini belum memiliki tampilan khusus.</p>
                    @endif
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                        ← Kembali ke Kursus
                    </a>
                    @auth
                        @if (!Auth::user()->completedContents->contains($content))
                            <form action="{{ route('contents.complete', $content) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Tandai Selesai') }}
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-md text-xs font-semibold">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Selesai
                            </span>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>