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
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-700">Course: {{ $selectedCourse->title }}</h2>
                    
                    @if($participants->count() > 0)
                        <form method="POST" action="{{ route('admin.auto-grade.complete-all') }}" class="inline">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm" 
                                    onclick="return confirm('Are you sure you want to automatically complete grading for ALL participants in this course?')">
                                Complete All Grading
                            </button>
                        </form>
                    @endif
                </div>
                
                @if($participants->count() > 0)
                    <!-- Summary Statistics -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <div class="text-sm text-blue-800">Total Participants</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $participants->count() }}</div>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <div class="text-sm text-yellow-800">With Pending Grades</div>
                            <div class="text-2xl font-bold text-yellow-600">{{ $participants->filter(function($p) { return $p['pending_submissions']->count() > 0; })->count() }}</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <div class="text-sm text-green-800">Fully Graded</div>
                            <div class="text-2xl font-bold text-green-600">{{ $participants->filter(function($p) { return $p['pending_submissions']->count() == 0; })->count() }}</div>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <div class="text-sm text-purple-800">Avg. Progress</div>
                            <div class="text-2xl font-bold text-purple-600">
                                {{ $participants->count() > 0 ? round($participants->avg('progress_percentage')) : 0 }}%
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            @if($participants->count() > 0)
                <!-- Search and Filter -->
                <div class="mb-4 flex flex-wrap gap-2">
                    <input type="text" id="searchInput" placeholder="Search participants..." class="flex-1 min-w-[200px] rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <select id="filterSelect" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="all">All Participants</option>
                        <option value="pending">With Pending Grades</option>
                        <option value="completed">Fully Graded</option>
                    </select>
                </div>
                
                <!-- Participants List -->
                <div id="participantsList" class="space-y-3">
                    @foreach($participants as $participantData)
                        @php
                            $participant = $participantData['user'];
                            $pendingSubmissions = $participantData['pending_submissions'];
                            $allSubmissions = $participantData['all_submissions'];
                            $progress = $participantData['progress'];
                        @endphp
                        
                        <div class="participant-card border rounded-lg bg-gray-50 hover:shadow-md transition-shadow" 
                             data-name="{{ strtolower($participant->name) }}"
                             data-status="{{ $pendingSubmissions->count() > 0 ? 'pending' : 'completed' }}">
                            <!-- Participant Header -->
                            <div class="participant-header p-4 cursor-pointer flex justify-between items-center hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4 pointer-events-none">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-800 font-bold">{{ substr($participant->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold">{{ $participant->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $participant->email }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <!-- Progress Bar -->
                                    <div class="w-32 pointer-events-none">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span>Progress</span>
                                            <span>{{ $participantData['progress_percentage'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $participantData['progress_percentage'] }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Status Indicators -->
                                    <div class="text-center pointer-events-none">
                                        @if($pendingSubmissions->count() > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $pendingSubmissions->count() }} Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <div class="pointer-events-auto">
                                        @if($pendingSubmissions->count() > 0)
                                            <form method="POST" action="{{ route('admin.auto-grade.complete') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                                                <input type="hidden" name="user_id" value="{{ $participant->id }}">
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded"
                                                        onclick="event.stopPropagation(); return confirm('Are you sure you want to automatically complete grading for {{ $participant->name }}?')">
                                                    Complete
                                                </button>
                                            </form>
                                        @else
                                            <button class="bg-gray-300 text-gray-500 text-xs font-bold py-1 px-3 rounded cursor-not-allowed" disabled>
                                                Done
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Expand Icon -->
                                    <svg class="expand-icon w-5 h-5 text-gray-500 transform transition-transform duration-200 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Participant Details (Hidden by default) -->
                            <div class="participant-details hidden border-t border-gray-200 p-4 bg-white">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-medium text-sm mb-2">Progress Details</h4>
                                        <div class="text-sm space-y-1">
                                            <p>Contents Completed: <span class="font-medium">{{ $participantData['completed_contents'] }} / {{ $participantData['total_contents'] }}</span></p>
                                            <p>Progress Percentage: <span class="font-medium">{{ $participantData['progress_percentage'] }}%</span></p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-medium text-sm mb-2">Submission Status</h4>
                                        <div class="text-sm space-y-1">
                                            <p>Pending Submissions: <span class="font-medium text-yellow-600">{{ $pendingSubmissions->count() }}</span></p>
                                            <p>Completed Submissions: <span class="font-medium text-green-600">{{ $allSubmissions->count() - $pendingSubmissions->count() }}</span></p>
                                            <p>Total Submissions: <span class="font-medium">{{ $allSubmissions->count() }}</span></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Debug Info (hapus setelah selesai debug) -->
                                <div class="mt-4 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs">
                                    <strong>Debug Info:</strong><br>
                                    Participant Data Keys: {{ implode(', ', array_keys($participantData)) }}<br>
                                    All Submissions Count: {{ isset($allSubmissions) ? $allSubmissions->count() : 'not set' }}<br>
                                    Pending Submissions Count: {{ $pendingSubmissions->count() }}<br>
                                    @if(isset($participantData['total_contents']))
                                        Total Contents: {{ $participantData['total_contents'] }}<br>
                                        Completed Contents: {{ $participantData['completed_contents'] }}<br>
                                    @endif
                                </div>

                                @if(isset($allSubmissions) && $allSubmissions->count() > 0)
                                    <div class="mt-4">
                                        <h4 class="font-medium text-sm mb-2">Essay Submissions in This Course</h4>
                                        <div class="max-h-40 overflow-y-auto">
                                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-2 py-1 text-left">Title</th>
                                                        <th class="px-2 py-1 text-left">Status</th>
                                                        <th class="px-2 py-1 text-left">Submitted</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @foreach($allSubmissions as $submission)
                                                        @php
                                                            $isPending = $pendingSubmissions->contains('id', $submission->id);
                                                        @endphp
                                                        <tr>
                                                            <td class="px-2 py-1">
                                                                {{ $submission->content ? $submission->content->title : 'Content not found' }}
                                                            </td>
                                                            <td class="px-2 py-1">
                                                                @if($isPending)
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                        Pending
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                        Completed
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="px-2 py-1 text-gray-500">
                                                                {{ $submission->created_at ? $submission->created_at->format('M j, Y') : 'N/A' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 p-3 bg-gray-100 rounded text-sm text-gray-600">
                                        <strong>No essay submissions found for this course.</strong><br>
                                        This might happen if:
                                        <ul class="mt-1 ml-4 list-disc text-xs">
                                            <li>The participant hasn't submitted any essays yet</li>
                                            <li>All submissions are in other courses</li>
                                            <li>There's an issue with the data relationship</li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No participants with essay submissions found in this course.</p>
            @endif
        @else
            <div class="text-center py-8 text-gray-500">
                <p>Please select a course to view participants with pending grades.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accordion functionality
    const participantCards = document.querySelectorAll('.participant-card');
    
    participantCards.forEach(card => {
        const header = card.querySelector('.participant-header');
        const details = card.querySelector('.participant-details');
        const icon = card.querySelector('.expand-icon');
        
        header.addEventListener('click', function(e) {
            // Prevent accordion toggle when clicking on buttons or forms
            if (e.target.closest('button, form')) {
                return;
            }
            
            // Toggle details visibility
            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                details.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        });
    });
    
    // Search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('filterSelect');
    
    if (searchInput && filterSelect) {
        function filterParticipants() {
            const searchTerm = searchInput.value.toLowerCase();
            const filterValue = filterSelect.value;
            
            participantCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const status = card.getAttribute('data-status');
                
                const matchesSearch = name.includes(searchTerm);
                const matchesFilter = filterValue === 'all' || 
                                    (filterValue === 'pending' && status === 'pending') || 
                                    (filterValue === 'completed' && status === 'completed');
                
                if (matchesSearch && matchesFilter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        searchInput.addEventListener('input', filterParticipants);
        filterSelect.addEventListener('change', filterParticipants);
    }
});
</script>
@endsection