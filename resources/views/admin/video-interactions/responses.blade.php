<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Responses: {{ $videoInteraction->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ ucfirst($videoInteraction->type) }} interaction at {{ gmdate("i:s", $videoInteraction->timestamp) }} min
                </p>
            </div>
            <a href="{{ route('admin.video-interactions.index', $content) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Interactions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Total Responses</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_responses']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600">Unique Users</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['unique_users']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($videoInteraction->type === 'quiz' || ($videoInteraction->type === 'reflection' && isset($videoInteraction->data['reflection_has_scoring']) && $videoInteraction->data['reflection_has_scoring']))
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600">
                                        @if($videoInteraction->type === 'quiz') Correct @else Optimal @endif
                                    </p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['correct_responses']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600">Success Rate</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['success_rate'], 1) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600">Avg. Rating</p>
                                    <p class="text-2xl font-semibold text-gray-900">N/A</p>
                                    <p class="text-xs text-gray-500">No scoring</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filters & Search</h3>
                    
                    <form method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Search User</label>
                                <input type="text" name="search" id="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Name or email..."
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- User Filter -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700">Specific User</label>
                                <select name="user_id" id="user_id" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Correctness Filter -->
                            @if($videoInteraction->type === 'quiz' || ($videoInteraction->type === 'reflection' && isset($videoInteraction->data['reflection_has_scoring']) && $videoInteraction->data['reflection_has_scoring']))
                                <div>
                                    <label for="correctness" class="block text-sm font-medium text-gray-700">Correctness</label>
                                    <select name="correctness" id="correctness" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="all">All Responses</option>
                                        <option value="correct" {{ request('correctness') === 'correct' ? 'selected' : '' }}>
                                            @if($videoInteraction->type === 'quiz') Correct Only @else Optimal Only @endif
                                        </option>
                                        <option value="incorrect" {{ request('correctness') === 'incorrect' ? 'selected' : '' }}>
                                            @if($videoInteraction->type === 'quiz') Incorrect Only @else Sub-optimal Only @endif
                                        </option>
                                    </select>
                                </div>
                            @endif

                            <!-- Date Range -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700">Date Range</label>
                                <div class="flex space-x-2">
                                    <input type="date" name="date_from" id="date_from" 
                                           value="{{ request('date_from') }}"
                                           class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <input type="date" name="date_to" id="date_to" 
                                           value="{{ request('date_to') }}"
                                           class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex space-x-3">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Apply Filters
                                </button>
                                
                                @if(request()->hasAny(['search', 'user_id', 'correctness', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.video-interactions.responses', [$content, $videoInteraction]) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Clear Filters
                                    </a>
                                @endif
                            </div>

                            <div class="text-sm text-gray-500">
                                Showing {{ $responses->firstItem() ?? 0 }}-{{ $responses->lastItem() ?? 0 }} of {{ $responses->total() }} responses
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Responses List -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if($responses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                                    @if($videoInteraction->type === 'quiz' || ($videoInteraction->type === 'reflection' && isset($videoInteraction->data['reflection_has_scoring']) && $videoInteraction->data['reflection_has_scoring']))
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($responses as $response)
                                    <tr class="hover:bg-gray-50">
                                        <!-- User Info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($response->user->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $response->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $response->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Response Content -->
                                        <td class="px-6 py-4">
                                            @if($videoInteraction->type === 'quiz')
                                                @php
                                                    $selectedOption = $response->response_data['selected_option'] ?? null;
                                                    $options = $videoInteraction->data['options'] ?? [];
                                                @endphp
                                                @if(isset($options[$selectedOption]))
                                                    <div class="text-sm text-gray-900">{{ $options[$selectedOption]['text'] }}</div>
                                                    <div class="text-xs text-gray-500">Option {{ $selectedOption + 1 }}</div>
                                                @else
                                                    <span class="text-sm text-gray-500">Invalid option</span>
                                                @endif
                                            
                                            @elseif($videoInteraction->type === 'reflection')
                                                @php
                                                    $reflectionData = $videoInteraction->data ?? [];
                                                    $reflectionType = $reflectionData['reflection_type'] ?? 'text';
                                                @endphp
                                                
                                                @if($reflectionType === 'multiple_choice')
                                                    @php
                                                        $selectedOption = $response->response_data['selected_option'] ?? null;
                                                        $options = $reflectionData['reflection_options'] ?? [];
                                                    @endphp
                                                    @if(isset($options[$selectedOption]))
                                                        <div class="text-sm text-gray-900">{{ $options[$selectedOption]['text'] }}</div>
                                                        <div class="text-xs text-gray-500">Option {{ $selectedOption + 1 }}</div>
                                                    @else
                                                        <span class="text-sm text-gray-500">Invalid option</span>
                                                    @endif
                                                @else
                                                    <div class="text-sm text-gray-900 max-w-md">
                                                        {{ Str::limit($response->response_data['reflection_text'] ?? 'No text provided', 100) }}
                                                    </div>
                                                @endif
                                            @endif
                                        </td>

                                        <!-- Result (if applicable) -->
                                        @if($videoInteraction->type === 'quiz' || ($videoInteraction->type === 'reflection' && isset($videoInteraction->data['reflection_has_scoring']) && $videoInteraction->data['reflection_has_scoring']))
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($response->is_correct === true)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        @if($videoInteraction->type === 'quiz') ✓ Correct @else ✓ Optimal @endif
                                                    </span>
                                                @elseif($response->is_correct === false)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        @if($videoInteraction->type === 'quiz') ✗ Incorrect @else ✗ Sub-optimal @endif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        - No Score
                                                    </span>
                                                @endif
                                            </td>
                                        @endif

                                        <!-- Date & Time -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ \Carbon\Carbon::parse($response->answered_at)->format('M j, Y') }}</div>
                                            <div class="text-xs">{{ \Carbon\Carbon::parse($response->answered_at)->format('g:i A') }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $responses->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h2m0-13h2a2 2 0 002 2v11a2 2 0 01-2 2h-2m0-13V4a1 1 0 011-1h8a1 1 0 011 1v1m0 0V4a1 1 0 011-1h8a1 1 0 011 1v9.172a2 2 0 01-.586 1.414l-2 2a2 2 0 01-1.414.586H9.172a2 2 0 01-1.414-.586l-2-2A2 2 0 015 13.172V4a1 1 0 011-1h3.172"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Responses Found</h3>
                        <p class="text-gray-600 mb-6">
                            @if(request()->hasAny(['search', 'user_id', 'correctness', 'date_from', 'date_to']))
                                No responses match your current filters. Try adjusting your search criteria.
                            @else
                                No one has responded to this interaction yet. Responses will appear here when users interact with this element in the video.
                            @endif
                        </p>
                        
                        @if(request()->hasAny(['search', 'user_id', 'correctness', 'date_from', 'date_to']))
                            <a href="{{ route('admin.video-interactions.responses', [$content, $videoInteraction]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Clear All Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>