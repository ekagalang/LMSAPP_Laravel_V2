@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Audio Learning</h1>
        <p class="text-gray-600">Learn English through interactive listening exercises</p>
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

                    <!-- Action Button -->
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