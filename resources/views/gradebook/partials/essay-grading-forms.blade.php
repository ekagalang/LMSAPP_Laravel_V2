@if($submission->content->grading_mode === 'individual' && $submission->content->scoring_enabled)
    <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
        <h4 class="font-bold text-blue-900 mb-4">Penilaian Individual (Dengan Scoring)</h4>
        
        <form action="{{ route('gradebook.store-multi-grade', $submission) }}" method="POST">
            @csrf
            @foreach($submission->answers as $index => $answer)
                <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-white">
                    <h5 class="font-medium text-gray-800 mb-2">Pertanyaan {{ $index + 1 }}</h5>
                    
                    @if($answer->question)
                        <div class="text-gray-700 mb-3">{!! $answer->question->question !!}</div>
                    @endif
                    
                    <div class="bg-gray-50 p-3 rounded mb-3">
                        <strong>Jawaban:</strong> {!! $answer->answer !!}
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nilai</label>
                            <input type="number" 
                                   name="scores[{{ $answer->id }}]" 
                                   value="{{ $answer->score }}"
                                   class="w-full border-gray-300 rounded-lg" 
                                   min="0" max="100">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
                            <textarea name="feedback[{{ $answer->id }}]" 
                                      rows="3"
                                      class="w-full border-gray-300 rounded-lg">{{ $answer->feedback }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Simpan Nilai Individual
            </button>
        </form>
    </div>

{{-- Individual Grading tanpa Scoring --}}
@elseif($submission->content->grading_mode === 'individual' && !$submission->content->scoring_enabled)
    <div class="bg-green-50 rounded-xl p-6 border border-green-200">
        <h4 class="font-bold text-green-900 mb-4">Feedback Individual (Tanpa Scoring)</h4>
        
        <form action="{{ route('gradebook.store-multi-grade', $submission) }}" method="POST">
            @csrf
            @foreach($submission->answers as $index => $answer)
                <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-white">
                    <h5 class="font-medium text-gray-800 mb-2">Pertanyaan {{ $index + 1 }}</h5>
                    
                    @if($answer->question)
                        <div class="text-gray-700 mb-3">{!! $answer->question->question !!}</div>
                    @endif
                    
                    <div class="bg-gray-50 p-3 rounded mb-3">
                        <strong>Jawaban:</strong> {!! $answer->answer !!}
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
                        <textarea name="feedback[{{ $answer->id }}]" 
                                  rows="4"
                                  class="w-full border-gray-300 rounded-lg">{{ $answer->feedback }}</textarea>
                    </div>
                </div>
            @endforeach
            
            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Simpan Feedback Individual
            </button>
        </form>
    </div>

{{-- Overall Grading dengan Scoring --}}
@elseif($submission->content->grading_mode === 'overall' && $submission->content->scoring_enabled)
    <div class="bg-purple-50 rounded-xl p-6 border border-purple-200">
        <h4 class="font-bold text-purple-900 mb-4">Penilaian Overall (Dengan Scoring)</h4>
        
        {{-- Tampilkan semua soal dan jawaban --}}
        <div class="mb-6">
            @foreach($submission->answers as $index => $answer)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg bg-white">
                    @if($answer->question)
                        <h5 class="font-medium text-gray-800 mb-2">Pertanyaan {{ $index + 1 }}: {{ $answer->question->question }}</h5>
                    @endif
                    <div class="bg-gray-50 p-3 rounded">
                        <strong>Jawaban:</strong> {!! $answer->answer !!}
                    </div>
                </div>
            @endforeach
        </div>
        
        <form action="{{ route('gradebook.store-overall-grade', $submission) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Overall (0-100)</label>
                    <input type="number" 
                           name="overall_score" 
                           value="{{ $submission->answers->first()->score ?? '' }}"
                           class="w-full border-gray-300 rounded-lg text-center text-2xl font-bold" 
                           min="0" max="100" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Feedback Overall</label>
                    <textarea name="overall_feedback" 
                              rows="4"
                              class="w-full border-gray-300 rounded-lg">{{ $submission->answers->first()->feedback ?? '' }}</textarea>
                </div>
            </div>
            
            <button type="submit" class="mt-4 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Simpan Nilai Overall
            </button>
        </form>
    </div>

{{-- Overall Grading tanpa Scoring --}}
@else
    <div class="bg-orange-50 rounded-xl p-6 border border-orange-200">
        <h4 class="font-bold text-orange-900 mb-4">Feedback Overall (Tanpa Scoring)</h4>
        
        {{-- Tampilkan semua soal dan jawaban --}}
        <div class="mb-6">
            @foreach($submission->answers as $index => $answer)
                <div class="mb-4 p-4 border border-gray-200 rounded-lg bg-white">
                    @if($answer->question)
                        <h5 class="font-medium text-gray-800 mb-2">Pertanyaan {{ $index + 1 }}: {{ $answer->question->question }}</h5>
                    @endif
                    <div class="bg-gray-50 p-3 rounded">
                        <strong>Jawaban:</strong> {!! $answer->answer !!}
                    </div>
                </div>
            @endforeach
        </div>
        
        <form action="{{ route('gradebook.storeEssayFeedbackOnly', $submission) }}" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Feedback untuk Keseluruhan Essay</label>
                <textarea name="feedback" 
                          rows="6"
                          class="w-full border-gray-300 rounded-lg"
                          placeholder="Berikan feedback konstruktif untuk keseluruhan essay..."
                          required>{{ $submission->answers->first()->feedback ?? '' }}</textarea>
            </div>
            
            <button type="submit" class="mt-4 px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                Simpan Feedback Overall
            </button>
        </form>
    </div>
@endif