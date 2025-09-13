<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Video Interactions - {{ $content->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kelola elemen interaktif dalam video</p>
            </div>
            <a href="{{ route('admin.video-interactions.create', $content) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Interaksi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Content Info Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start space-x-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $content->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $content->description }}</p>
                            @if($content->body)
                                <a href="{{ $content->body }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                    🔗 {{ $content->body }}
                                </a>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst($content->type) }}
                            </span>
                            <p class="text-sm text-gray-500 mt-2">{{ $interactions->count() }} Interactions</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactions List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if($interactions->count() > 0)
                        <div class="space-y-4">
                            @foreach($interactions as $interaction)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                    @if($interaction->type === 'quiz') bg-blue-100 text-blue-800
                                                    @elseif($interaction->type === 'reflection') bg-purple-100 text-purple-800
                                                    @elseif($interaction->type === 'annotation') bg-yellow-100 text-yellow-800
                                                    @elseif($interaction->type === 'hotspot') bg-green-100 text-green-800
                                                    @elseif($interaction->type === 'overlay') bg-indigo-100 text-indigo-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    @if($interaction->type === 'quiz') 🧠 Quiz
                                                    @elseif($interaction->type === 'reflection') 🤔 Reflection
                                                    @elseif($interaction->type === 'annotation') 📝 Annotation
                                                    @elseif($interaction->type === 'hotspot') 📍 Hotspot
                                                    @elseif($interaction->type === 'overlay') 🎯 Overlay
                                                    @else ⏸️ Pause @endif
                                                </span>
                                                
                                                <span class="text-sm font-medium text-gray-600">
                                                    ⏱️ {{ gmdate("i:s", $interaction->timestamp) }} min
                                                </span>

                                                @if(!$interaction->is_active)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <h4 class="text-lg font-semibold text-gray-900 mt-2">{{ $interaction->title }}</h4>
                                            <p class="text-gray-600 mt-1">{{ $interaction->description }}</p>

                                            <!-- Show responses count -->
                                            @if($interaction->responses->count() > 0)
                                                <div class="mt-3 text-sm text-gray-500">
                                                    📊 {{ $interaction->responses->count() }} responses
                                                    @if($interaction->type === 'quiz')
                                                        ({{ $interaction->getCorrectResponsesCount() }} correct, 
                                                        {{ number_format($interaction->getSuccessRate(), 1) }}% success rate)
                                                    @elseif($interaction->type === 'reflection')
                                                        @php
                                                            $reflectionData = $interaction->data ?? [];
                                                            $hasScoring = $reflectionData['reflection_has_scoring'] ?? false;
                                                        @endphp
                                                        @if($hasScoring && $interaction->type === 'reflection' && isset($reflectionData['reflection_type']) && $reflectionData['reflection_type'] === 'multiple_choice')
                                                            ({{ $interaction->getCorrectResponsesCount() }} optimal responses)
                                                        @endif
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-2 ml-4">
                                            @if($interaction->responses->count() > 0)
                                                <a href="{{ route('admin.video-interactions.responses', [$content, $interaction]) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                    Responses ({{ $interaction->responses->count() }})
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('admin.video-interactions.edit', [$content, $interaction]) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.video-interactions.destroy', [$content, $interaction]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Yakin ingin menghapus interaksi ini?');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Interaksi Video</h3>
                            <p class="text-gray-600 mb-6">Tambahkan elemen interaktif seperti quiz, annotation, atau hotspot untuk membuat video lebih engaging.</p>
                            <a href="{{ route('admin.video-interactions.create', $content) }}" 
                               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Interaksi Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('contents.show', $content) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Content
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.fixed.top-4.right-4').remove();
            }, 3000);
        </script>
    @endif
</x-app-layout>