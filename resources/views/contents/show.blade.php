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
                    {{-- ======================================================================= --}}
                    {{-- KONTEN VIDEO --}}
                    {{-- ======================================================================= --}}
                    @if ($content->type === 'video')
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe src="{{ $content->video_url }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="mt-4 prose max-w-none">
                            {!! $content->description !!}
                        </div>

                    {{-- ======================================================================= --}}
                    {{-- KONTEN TEKS --}}
                    {{-- ======================================================================= --}}
                    @elseif ($content->type === 'text')
                        <div class="prose max-w-none">
                            {!! $content->description !!}
                        </div>

                    {{-- ======================================================================= --}}
                    {{-- KONTEN KUIS --}}
                    {{-- ======================================================================= --}}
                    @elseif ($content->type === 'quiz')
                        <div class="prose max-w-none">
                            <h2 class="text-2xl font-bold">{{ $content->quiz->title }}</h2>
                            {!! $content->quiz->description !!}
                        </div>
                        @auth
                            @if (Auth::user()->hasRole('participant'))
                                @php
                                    $attempt = Auth::user()->quizAttempts()->where('quiz_id', $content->quiz_id)->latest()->first();
                                @endphp
                                <div class="mt-6">
                                    @if ($attempt && !$attempt->is_completed)
                                        <a href="{{ route('quizzes.attempt', $content->quiz) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">Lanjutkan Kuis</a>
                                    @elseif ($attempt && $attempt->is_completed)
                                        <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                                            Anda telah menyelesaikan kuis ini dengan skor: <strong>{{ $attempt->score }}</strong>.
                                        </div>
                                    @else
                                        <a href="{{ route('quizzes.start', $content->quiz) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Mulai Kuis</a>
                                    @endif
                                </div>
                            @else
                                <div class="mt-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
                                    <p>Ini adalah pratinjau kuis untuk instruktur. Peserta akan melihat tombol untuk memulai kuis.</p>
                                </div>
                            @endif
                        @endauth

                    {{-- ======================================================================= --}}
                    {{-- KONTEN ESAI (YANG BARU) --}}
                    {{-- ======================================================================= --}}
                    @elseif ($content->type == 'essay')
                        @php
                            // Cek apakah participant sudah pernah submit
                            $submission = $content->essaySubmissions()->where('user_id', Auth::id())->first();
                        @endphp

                        {{-- Jika sudah submit, tampilkan hasil submitnya --}}
                        @if ($submission)
                            <div class="p-4 my-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                                <span class="font-medium">Anda sudah mengirimkan esai ini.</span>
                            </div>
                            <div class="p-6 bg-white border rounded-lg mt-4">
                                <h3 class="text-xl font-semibold mb-2">Jawaban Anda:</h3>
                                <div class="prose max-w-none">{!! $submission->content !!}</div>

                                @if($submission->grade)
                                    <div class="mt-4 pt-4 border-t">
                                        <strong class="text-lg">Nilai:</strong> 
                                        <span class="text-xl font-bold text-blue-600">{{ $submission->grade }}</span>
                                    </div>
                                @endif
                                @if($submission->feedback)
                                    <div class="mt-4">
                                        <strong class="text-lg">Feedback:</strong>
                                        <div class="mt-1 prose max-w-none">{!! $submission->feedback !!}</div>
                                    </div>
                                @endif
                            </div>
                        {{-- Jika belum submit dan merupakan participant, tampilkan editor --}}
                        @elseif(Auth::user()->hasRole('participant'))
                            <form action="{{ route('essay.submit', $content->id) }}" method="POST" class="mt-6">
                                @csrf
                                <h3 class="text-xl font-semibold mb-2">Tulis Jawaban Anda:</h3>

                                {{-- Gunakan komponen tinymce-editor dengan nama yang benar --}}
                                <x-forms.tinymce-editor name="essay_content" />

                                <div class="mt-4">
                                    <x-primary-button>
                                        {{ __('Kirim Jawaban') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        @endif
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
    @stack('scripts')
</x-app-layout>

@push('scripts')
<x-head.tinymce-config/>
<script src="https://cdn.tiny.cloud/1/wfo9boig39silkud2152anvh7iaqnu9wf4wqh75iudy3mry6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // Inisialisasi TinyMCE hanya jika ada textarea #answer
    if (document.getElementById('answer')) {
        tinymce.init({
            selector: 'textarea#answer',
            plugins: 'lists link autolink wordcount',
            toolbar: 'undo redo | blocks | bold italic | bullist numlist',
            branding: false,
            menubar: false,

            setup: function (editor) {
                editor.on('change', function () {
                    editor.save(); // Perintah ini menyalin isi editor ke textarea asli
                });
            }
        });
    }
</script>
@endpush