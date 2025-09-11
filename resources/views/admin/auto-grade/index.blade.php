@extends('layouts.app')

@section('title', 'Automatic Grading Completion')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Automatic Grading Completion</h1>
        
        <!-- Course Selection Form -->
        <form method="GET" action="{{ route('admin.auto-grade.index') }}" class="mb-6">
            <div class="mb-4">
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Select Course</label>
                <select id="course_id" name="course_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">-- Select a Course --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (isset($selectedCourse) && $selectedCourse && $selectedCourse->id == $course->id) ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Show Participants
            </button>
        </form>

        <!-- Display selected course and participants -->
        @if(isset($selectedCourse) && $selectedCourse)
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-700">Course: {{ $selectedCourse->title }}</h2>
                
                @if($participants->count() > 0)
                    <form method="POST" action="{{ route('admin.auto-grade.complete-all') }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" 
                                onclick="return confirm('Are you sure you want to automatically complete grading for ALL participants in this course?')">
                            Complete All Grading for This Course
                        </button>
                    </form>
                @endif
            </div>
            
            @if($participants->count() > 0)
                <div class="space-y-4">
                    @foreach($participants as $participantData)
                        @php
                            $participant = $participantData['user'];
                            $pendingSubmissions = $participantData['pending_submissions'];
                            $allSubmissions = $participantData['all_submissions'];
                            $progress = $participantData['progress'];
                        @endphp
                        
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $participant->name }}</h3>
                                    <p class="text-gray-600">{{ $participant->email }}</p>
                                    <div class="mt-2">
                                        <p class="text-sm">
                                            <span class="font-medium">Progress:</span> 
                                            {{ $participantData['completed_contents'] }} / {{ $participantData['total_contents'] }} contents 
                                            ({{ $participantData['progress_percentage'] }}%)
                                        </p>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm">
                                            <span class="font-medium">Pending Submissions:</span> 
                                            {{ $pendingSubmissions->count() }} essay(s)
                                        </p>
                                        @if($allSubmissions->count() > $pendingSubmissions->count())
                                            <p class="text-sm text-green-600">
                                                <span class="font-medium">Completed Submissions:</span> 
                                                {{ $allSubmissions->count() - $pendingSubmissions->count() }} essay(s)
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($pendingSubmissions->count() > 0)
                                    <form method="POST" action="{{ route('admin.auto-grade.complete') }}">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                                        <input type="hidden" name="user_id" value="{{ $participant->id }}">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                                onclick="return confirm('Are you sure you want to automatically complete grading for {{ $participant->name }}?')">
                                            Complete Grading
                                        </button>
                                    </form>
                                @else
                                    <button class="bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed" disabled>
                                        No Pending Grades
                                    </button>
                                @endif
                            </div>
                            
                            @if($allSubmissions->count() > 0)
                                <div class="mt-3 pl-4 border-l-2 border-gray-300">
                                    <h4 class="font-medium text-sm">All Essay Submissions:</h4>
                                    <ul class="mt-1 space-y-1">
                                        @foreach($allSubmissions as $submission)
                                            @php
                                                $isPending = $pendingSubmissions->contains('id', $submission->id);
                                            @endphp
                                            <li class="text-sm {{ $isPending ? 'text-red-600' : 'text-green-600' }}">
                                                - {{ $submission->content->title }} 
                                                {{ $isPending ? '(Pending)' : '(Completed)' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No participants with essay submissions found in this course.</p>
            @endif
        @else
            <div class="text-center py-8 text-gray-500">
                <p>Please select a course to view participants with pending grades.</p>
            </div>
        @endif
    </div>
</div>

@endsection