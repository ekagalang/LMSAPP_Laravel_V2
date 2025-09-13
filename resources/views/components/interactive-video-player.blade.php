@props(['content', 'userId'])

@php
    $hasInteractions = $content->hasInteractiveVideo();
    $videoId = $content->getYoutubeVideoIdAttribute();
@endphp

<div class="video-container">
    <!-- Video Description -->
    @if($content->description)
        <div class="prose max-w-none text-gray-700 leading-relaxed mb-8">
            {!! $content->description !!}
        </div>
    @endif

    @if($hasInteractions && $videoId)
        <!-- Interactive Video Player -->
        <div class="interactive-video-container">
            <div id="interactive-video-player-{{ $content->id }}" class="video-loading">
                Loading interactive video...
            </div>
        </div>
        
        <!-- Video Progress Information -->
        <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Interactive Learning Video</h3>
                    <p class="text-gray-600">This video contains interactive elements that will enhance your learning experience.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600" id="interaction-count">
                            {{ $content->videoInteractions()->count() }}
                        </div>
                        <div class="text-sm text-gray-500">Interactions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600" id="progress-percentage">0%</div>
                        <div class="text-sm text-gray-500">Complete</div>
                    </div>
                    @can('update', $content)
                        <div class="text-center">
                            <a href="{{ route('admin.video-interactions.index', $content) }}" 
                               class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Kelola
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4 bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>

        @push('styles')
            <link rel="stylesheet" href="{{ asset('css/interactive-video.css') }}">
        @endpush

        @push('scripts')
            <script src="{{ asset('js/interactive-video-player.js') }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('=== Interactive Video Player Init ===');
                    console.log('Content ID:', {{ $content->id }});
                    console.log('User ID:', {{ $userId }});
                    console.log('Video ID:', '{{ $videoId }}');
                    
                    // Initialize interactive video player
                    const playerContainer = 'interactive-video-player-{{ $content->id }}';
                    
                    // Load video interactions from API with timeout for better performance
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
                    
                    fetch(`/api/video-interactions/content/{{ $content->id }}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        signal: controller.signal
                    })
                    .then(response => {
                        clearTimeout(timeoutId);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        
                        // Update interaction count immediately
                        const countElement = document.getElementById('interaction-count');
                        if (countElement && data.interactions) {
                            countElement.textContent = data.interactions.length;
                        }
                        
                        // Initialize player with interactions
                        if (typeof InteractiveVideoPlayer !== 'undefined') {
                            window.videoPlayer = new InteractiveVideoPlayer(playerContainer, {
                                contentId: {{ $content->id }},
                                userId: {{ $userId }},
                                interactions: data.interactions || [],
                                csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                videoSrc: {
                                    type: 'youtube',
                                    src: 'https://www.youtube.com/watch?v={{ $videoId }}'
                                }
                            });

                            // Load user progress
                            loadUserProgress();
                        } else {
                            // Fallback to regular player if InteractiveVideoPlayer not available
                            showFallbackPlayer();
                        }
                    })
                    .catch(error => {
                        clearTimeout(timeoutId);
                        // Log only in development
                        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
                            console.error('Error loading video interactions:', error.message);
                        }
                        // Fallback to regular YouTube player
                        showFallbackPlayer();
                    });

                    function loadUserProgress() {
                        fetch(`/api/video-interactions/progress/{{ $content->id }}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.progress) {
                                updateProgressDisplay(data.progress);
                            }
                        })
                        .catch(error => {
                            // Silent fail for progress loading
                        });
                    }

                    function updateProgressDisplay(progress) {
                        const progressBar = document.getElementById('progress-bar');
                        const progressPercentage = document.getElementById('progress-percentage');
                        
                        if (progressBar && progressPercentage) {
                            progressBar.style.width = progress.completion_percentage + '%';
                            progressPercentage.textContent = Math.round(progress.completion_percentage) + '%';
                        }
                    }

                    function showFallbackPlayer() {
                        const container = document.getElementById(playerContainer);
                        if (container) {
                            container.innerHTML = `
                                <div class="aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black">
                                    <iframe
                                        class="w-full h-full"
                                        src="{{ $content->youtube_embed_url }}"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            `;
                        }
                    }
                });
            </script>
        @endpush

    @else
        <!-- Regular YouTube Player (Fallback) -->
        <div class="aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black">
            <iframe
                class="w-full h-full"
                src="{{ $content->youtube_embed_url }}"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                onerror="this.style.display='none'; document.getElementById('youtube-fallback-{{ $content->id }}').style.display='block';">
            </iframe>
        </div>
        
        <!-- Fallback untuk iframe error -->
        <div id="youtube-fallback-{{ $content->id }}" style="display:none;" class="mt-4">
            <p class="text-center text-yellow-600 bg-yellow-100 p-4 rounded-lg">
                Video tidak dapat diputar di sini.
            </p>
            <a href="{{ $content->body }}" target="_blank" rel="noopener noreferrer" class="block group mt-2">
                <div class="relative rounded-2xl overflow-hidden shadow-lg">
                    <img src="{{ $content->youtube_thumbnail_url }}" alt="Video thumbnail" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center group-hover:bg-opacity-60 transition-all duration-300">
                        <div class="text-center text-white">
                            <svg class="w-20 h-20 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path>
                            </svg>
                            <p class="font-bold text-xl mt-2">Tonton di YouTube</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
</div>