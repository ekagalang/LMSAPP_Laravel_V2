@if ($content->type == 'essay')
    @php
        // Cek apakah peserta sudah pernah mengirimkan jawaban untuk esai ini
        $submission = $content->essaySubmissions()->where('user_id', Auth::id())->first();
    @endphp

    <div class="mt-6 border-t pt-6">
        {{-- JIKA SUDAH ADA JAWABAN --}}
        @if ($submission)
            <h3 class="text-xl font-semibold mb-2 text-gray-800">Jawaban Anda:</h3>
            <div class="prose max-w-none p-4 bg-gray-100 rounded-md border">
                {!! $submission->answer !!}
            </div>
        {{-- JIKA BELUM ADA JAWABAN DAN USER ADALAH PESERTA --}}
        @elseif (Auth::user()->hasRole('participant'))
            <form action="{{ route('essays.store', $content) }}" method="POST" class="mt-6">
                @csrf
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Tulis Jawaban Anda:</h3>
                
                {{-- Textarea ini akan diubah menjadi editor oleh TinyMCE --}}
                <textarea name="essay_content" id="essay_editor"></textarea>
                
                <div class="mt-4">
                    <x-primary-button>{{ __('Kirim Jawaban') }}</x-primary-button>
                </div>
            </form>

            {{-- ✅ PERBAIKAN KUNCI DI SINI --}}
            {{-- Skrip ini hanya akan dimuat jika form jawaban ditampilkan --}}
            @push('scripts')
                {{-- Memuat library TinyMCE dari CDN --}}
                <script src="https://cdn.tiny.cloud/1/wfo9boig39silkud2152anvh7iaqnu9wf4wqh75iudy3mry6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
                <script>
                    // Menjalankan TinyMCE setelah halaman dimuat
                    document.addEventListener('DOMContentLoaded', function () {
                        if (document.getElementById('essay_editor')) {
                            tinymce.init({
                                selector: 'textarea#essay_editor',
                                plugins: 'code table lists link image media autosave wordcount fullscreen template',
                                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link image media',
                                branding: false,
                                menubar: true, // Menyederhanakan tampilan editor
                                setup: function (editor) {
                                    editor.on('change', function () {
                                        editor.save(); // Sinkronkan konten ke textarea
                                    });
                                }
                            });
                        }
                    });
                </script>
            @endpush
        @endif
    </div>

@elseif ($content->type == 'quiz' && $content->quiz)
    <div class="mt-6 border-t pt-6">
        <a href="{{ route('quizzes.start', $content->quiz->id) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-700">
            Mulai Kuis
        </a>
    </div>
@endif