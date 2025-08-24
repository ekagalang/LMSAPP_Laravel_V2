@props(['zoomSessions'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900">Zoom Meeting Yang Akan Datang</h3>
        </div>
    </div>
    <div class="p-6">
        @if($zoomSessions->count() > 0)
            <div class="space-y-4">
                @foreach($zoomSessions as $session)
                    @php
                        $zoomDetails = json_decode($session->body, true);
                        $schedulingStatus = $session->getSchedulingStatus();
                    @endphp
                    <div class="border rounded-lg p-4
                        @if($schedulingStatus['status'] === 'active') bg-green-50 border-green-200
                        @elseif($schedulingStatus['status'] === 'upcoming') bg-blue-50 border-blue-200
                        @else bg-gray-50 border-gray-200 @endif">

                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 mb-1">{{ $session->title }}</h4>
                                <p class="text-xs text-gray-600 mb-2">{{ $session->lesson->title }} - {{ $session->lesson->course->title }}</p>

                                @if($session->is_scheduled)
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $session->scheduled_start->format('d M Y, H:i') }} - {{ $session->scheduled_end->format('H:i') }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2">
                                @if($schedulingStatus['status'] === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <div class="w-2 h-2 bg-green-600 rounded-full mr-1 animate-pulse"></div>
                                        Sedang Berlangsung
                                    </span>
                                @elseif($schedulingStatus['status'] === 'upcoming')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-1"></div>
                                        Akan Dimulai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tersedia
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1v11a2 2 0 01-2 2H6a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h4z"></path>
                                    </svg>
                                    ID: {{ $zoomDetails['meeting_id'] ?? 'N/A' }}
                                </div>
                                @if(!empty($zoomDetails['password']))
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-6 6h-2m0 0H9m0 0H7a6 6 0 01-6-6 6 6 0 016-6h2m4 0V9a4 4 0 00-8 0v2"></path>
                                        </svg>
                                        Password: {{ $zoomDetails['password'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex space-x-2">
                                @if($schedulingStatus['can_join'])
                                    <a href="{{ $zoomDetails['link'] ?? '#' }}" target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-white
                                              @if($schedulingStatus['status'] === 'active') bg-green-600 hover:bg-green-700
                                              @else bg-blue-600 hover:bg-blue-700 @endif transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        Join Meeting
                                    </a>
                                @else
                                    <button disabled class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs leading-4 font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2-2v6a2 2 0 002 2z"></path>
                                        </svg>
                                        @if($schedulingStatus['status'] === 'upcoming')
                                            Belum Dimulai
                                        @else
                                            Sudah Berakhir
                                        @endif
                                    </button>
                                @endif

                                <a href="{{ route('contents.show', $session->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Detail
                                </a>
                            </div>
                        </div>

                        @if($session->is_scheduled && $schedulingStatus['status'] === 'upcoming')
                            <div class="mt-3 text-xs text-gray-600 bg-blue-50 p-2 rounded">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $schedulingStatus['message'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <p class="text-sm">Tidak ada zoom meeting yang akan datang.</p>
            </div>
        @endif
    </div>
</div>
