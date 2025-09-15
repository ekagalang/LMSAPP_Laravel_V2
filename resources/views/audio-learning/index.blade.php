@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Microlearning</h1>
            <p class="text-gray-600">Learn English through interactive audio and video lessons</p>
        </div>

        @if(Auth::check() && Auth::user()->hasRole(['super-admin', 'instructor']))
            <a href="{{ route('audio-learning.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Microlearning
            </a>
        @endif
    </div>

    <!-- Difficulty Filter -->
    <div class="mb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('audio-learning.index', ['difficulty' => 'all']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ $difficulty === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                All Levels
            </a>
            <a href="{{ route('audio-learning.index', ['difficulty' => 'beginner']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ $difficulty === 'beginner' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Beginner
            </a>
            <a href="{{ route('audio-learning.index', ['difficulty' => 'intermediate']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ $difficulty === 'intermediate' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Intermediate
            </a>
            <a href="{{ route('audio-learning.index', ['difficulty' => 'advanced']) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ $difficulty === 'advanced' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Advanced
            </a>
        </div>
    </div>

    <!-- Lessons Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($lessons as $lesson)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Lesson Header -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                                   {{ $lesson->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' :
                                      ($lesson->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' :
                                       'bg-red-100 text-red-800') }}">
                            {{ ucfirst($lesson->difficulty_level) }}
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $lesson->formatted_duration }}
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $lesson->title }}</h3>
                    @if($lesson->description)
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($lesson->description, 120) }}</p>
                    @endif

                    <!-- Progress Bar -->
                    @auth
                        @if(isset($userProgress[$lesson->id]))
                            @php
                                $progress = $userProgress[$lesson->id];
                                $percentage = $progress['total'] > 0 ? round(($progress['completed'] / $progress['total']) * 100) : 0;
                            @endphp
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $progress['completed'] }}/{{ $progress['total'] }} exercises</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                @if($progress['max_score'] > 0)
                                    <div class="text-right text-sm text-gray-500 mt-1">
                                        Score: {{ $progress['score'] }}/{{ $progress['max_score'] }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endauth

                    <!-- Exercise Count -->
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            {{ $lesson->exercises->count() }} exercises
                        </div>

                        @if($lesson->exercises->sum('points') > 0)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                {{ $lesson->exercises->sum('points') }} points
                            </div>
                        @endif
                    </div>

                    <!-- Content Type Badge -->
                    <div class="flex items-center mb-4">
                        <span class="flex items-center text-sm text-gray-600">
                            {!! $lesson->getContentTypeIcon() !!}
                            <span class="ml-1 capitalize">{{ $lesson->content_type }}</span>
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    @if(Auth::check() && Auth::user()->hasRole(['super-admin', 'instructor']))
                        <!-- Admin Actions -->
                        <div class="flex gap-2 mb-3">
                            <a href="{{ route('audio-learning.edit', $lesson->id) }}"
                               class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-3 rounded-lg transition-colors text-center text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('audio-learning.destroy', $lesson->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this microlearning lesson? This action cannot be undone.')"
                                        class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>

                        <!-- Learning Action -->
                        <a href="{{ route('audio-learning.lesson', $lesson->id) }}"
                           class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview Lesson
                        </a>
                    @else
                        <!-- Student Action -->
                        <a href="{{ route('audio-learning.lesson', $lesson->id) }}"
                           class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                            @auth
                                @if(isset($userProgress[$lesson->id]) && $userProgress[$lesson->id]['completed'] === $userProgress[$lesson->id]['total'])
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Review Lesson
                                @elseif(isset($userProgress[$lesson->id]))
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Continue Learning
                                @else
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Start Learning
                                @endif
                            @else
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                </svg>
                                Start Learning
                            @endauth
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 016 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No lessons available</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $difficulty !== 'all' ? 'No lessons found for ' . $difficulty . ' level.' : 'No audio lessons have been created yet.' }}
                    </p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection