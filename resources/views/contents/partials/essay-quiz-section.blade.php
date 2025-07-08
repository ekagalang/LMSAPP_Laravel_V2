@if ($content->type == 'essay')
    @php
        $submission = $content->essaySubmissions()->where('user_id', Auth::id())->first();
    @endphp

    <div class="mt-6 border-t pt-6">
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
        @elseif(Auth::user()->hasRole('participant'))
            <form action="{{ route('essay.submit', $content->id) }}" method="POST" class="mt-6">
                @csrf
                <h3 class="text-xl font-semibold mb-2">Tulis Jawaban Anda:</h3>
                <x-forms.tinymce-editor name="essay_content" />
                <div class="mt-4">
                    <x-primary-button>{{ __('Kirim Jawaban') }}</x-primary-button>
                </div>
            </form>
        @endif
    </div>
    @push('scripts')
        <x-head.tinymce-config/>
    @endpush
@elseif ($content->type == 'quiz' && $content->quiz)
    <div class="mt-6 border-t pt-6">
        <a href="{{ route('quizzes.start', $content->quiz->id) }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-semibold">
            Mulai Kuis
        </a>
    </div>
@endif