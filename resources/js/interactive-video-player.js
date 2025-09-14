// Interactive Video Player for YouTube videos with Quiz Support
class InteractiveVideoPlayer {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.contentId = options.contentId;
        this.interactions = options.interactions || [];
        this.userId = options.userId;
        this.csrfToken = options.csrfToken;
        this.videoSrc = options.videoSrc;
        this.player = null;
        this.currentTime = 0;
        this.activeInteractions = [];
        this.userResponses = {};
        
        this.init();
    }

    init() {
        // Load existing user responses from interactions data
        this.interactions.forEach(interaction => {
            if (interaction.user_response) {
                this.userResponses[interaction.id] = interaction.user_response;
            }
        });
        this.loadYouTubeAPI();
        this.showInteractionInfo();
        // Only log in development mode
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            console.log('Interactive video player initialized with', this.interactions.length, 'interactions');
        }
    }

    loadYouTubeAPI() {
        // Check if YouTube API is already loaded
        if (window.YT && window.YT.Player) {
            this.createVideoElement();
            return;
        }

        // Load YouTube API
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // Set callback for when API is ready
        window.onYouTubeIframeAPIReady = () => {
            this.createVideoElement();
        };
    }

    createVideoElement() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Container with id ${this.containerId} not found`);
            return;
        }

        // Extract YouTube video ID from URL
        const videoId = this.extractYouTubeId(this.videoSrc.src);
        
        if (!videoId) {
            console.error('Could not extract YouTube video ID from:', this.videoSrc.src);
            container.innerHTML = '<p class="text-red-500">Error: Invalid YouTube video URL</p>';
            return;
        }
        
        container.innerHTML = `
            <div class="relative aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black">
                <div id="${this.containerId}-player" class="w-full h-full"></div>
                ${this.interactions.length > 0 ? this.createInteractionOverlay() : ''}
                <div id="${this.containerId}-quiz-overlay" class="absolute inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                        <div id="quiz-content"></div>
                    </div>
                </div>
            </div>
        `;

        // Initialize YouTube player
        this.player = new YT.Player(`${this.containerId}-player`, {
            videoId: videoId,
            playerVars: {
                enablejsapi: 1,
                origin: window.location.origin
            },
            events: {
                onReady: this.onPlayerReady.bind(this),
                onStateChange: this.onPlayerStateChange.bind(this)
            }
        });
    }
    
    extractYouTubeId(url) {
        const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[7] && match[7].length === 11) ? match[7] : null;
    }

    onPlayerReady(event) {
        // Only log in development mode
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            console.log('YouTube player ready');
        }
        this.startTimeTracking();
    }

    onPlayerStateChange(event) {
        if (event.data === YT.PlayerState.PLAYING) {
            this.startTimeTracking();
        } else {
            this.stopTimeTracking();
        }
    }

    startTimeTracking() {
        if (this.timeTracker) {
            clearInterval(this.timeTracker);
        }
        
        // Optimize interval timing - use 250ms instead of 100ms for better performance
        // while still maintaining smooth interaction triggering
        this.timeTracker = setInterval(() => {
            if (this.player && this.player.getCurrentTime) {
                const newTime = this.player.getCurrentTime();
                // Only process if time actually changed significantly (avoid redundant checks)
                if (Math.abs(newTime - this.currentTime) > 0.1) {
                    this.currentTime = newTime;
                    this.checkForInteractions();
                }
            }
        }, 250); // Reduced from 100ms to 250ms for better performance
    }

    stopTimeTracking() {
        if (this.timeTracker) {
            clearInterval(this.timeTracker);
        }
    }

    checkForInteractions() {
        // Early exit if no interactions
        if (!this.interactions || this.interactions.length === 0) return;
        
        // Pre-filter interactions that are within reasonable time range (performance optimization)
        const timeWindow = 2; // Look ahead/behind 2 seconds
        const relevantInteractions = this.interactions.filter(interaction => 
            Math.abs(this.currentTime - interaction.timestamp) <= timeWindow
        );
        
        // Only check relevant interactions
        relevantInteractions.forEach(interaction => {
            // Check if interaction should be triggered (within 0.5 second tolerance)
            // Skip if user already responded to this interaction
            if (Math.abs(this.currentTime - interaction.timestamp) <= 0.5 && 
                !this.activeInteractions.includes(interaction.id) &&
                !this.userResponses[interaction.id] &&
                !interaction.user_response) {
                
                this.triggerInteraction(interaction);
            }
        });
    }

    triggerInteraction(interaction) {
        this.activeInteractions.push(interaction.id);
        
        switch(interaction.type) {
            case 'quiz':
                this.showQuiz(interaction);
                break;
            case 'reflection':
                this.showReflection(interaction);
                break;
            case 'pause':
                this.showPause(interaction);
                break;
            case 'overlay':
                this.showOverlay(interaction);
                break;
            case 'annotation':
                this.showAnnotation(interaction);
                break;
            case 'hotspot':
                this.showHotspot(interaction);
                break;
        }
    }

    showQuiz(interaction) {
        this.player.pauseVideo();
        
        const overlay = document.getElementById(`${this.containerId}-quiz-overlay`);
        const content = document.getElementById('quiz-content');
        
        if (!overlay || !content) return;

        const options = interaction.data.options || [];
        const correctAnswer = interaction.data.correct_answer || 0;

        content.innerHTML = `
            <h3 class="text-lg font-bold text-gray-800 mb-4">${interaction.title}</h3>
            <p class="text-gray-600 mb-4">${interaction.description || ''}</p>
            <form id="quiz-form-${interaction.id}" class="space-y-3">
                ${options.map((option, index) => `
                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="quiz_answer" value="${index}" class="mr-3">
                        <span>${option.text}</span>
                    </label>
                `).join('')}
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="window.videoPlayer.skipQuiz(${interaction.id})" 
                            class="px-4 py-2 text-gray-600 border rounded-lg hover:bg-gray-50">
                        Skip
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Submit Answer
                    </button>
                </div>
            </form>
            <div id="quiz-feedback-${interaction.id}" class="hidden mt-4 p-3 rounded-lg"></div>
        `;

        // Add event listener for form submission
        document.getElementById(`quiz-form-${interaction.id}`).addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitQuizAnswer(interaction, e.target);
        });

        overlay.classList.remove('hidden');
    }

    async submitQuizAnswer(interaction, form) {
        const formData = new FormData(form);
        const selectedAnswer = parseInt(formData.get('quiz_answer'));
        
        if (selectedAnswer === null || selectedAnswer === undefined) {
            this.showNotification('Silakan pilih jawaban terlebih dahulu', 'warning');
            return;
        }

        const responseData = {
            video_interaction_id: interaction.id,
            response_data: { selected_option: selectedAnswer },
            answered_at: new Date().toISOString()
        };

        try {
            const response = await fetch('/api/video-interactions/response', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(responseData)
            });

            const result = await response.json();

            if (response.ok) {
                this.userResponses[interaction.id] = result;
                this.showQuizFeedback(interaction, result);
                this.updateProgressIndicator();
                this.showNotification('Jawaban berhasil disimpan!', 'success');
            } else {
                console.error('Failed to submit quiz answer:', result);
                const errorMessage = result.error || result.message || 'Gagal menyimpan jawaban. Silakan coba lagi.';
                this.showNotification(errorMessage, 'error');
            }
        } catch (error) {
            console.error('Error submitting quiz answer:', error);
            this.showNotification('Koneksi bermasalah. Periksa koneksi internet Anda.', 'error');
        }
    }

    showQuizFeedback(interaction, result) {
        const feedbackDiv = document.getElementById(`quiz-feedback-${interaction.id}`);
        const form = document.getElementById(`quiz-form-${interaction.id}`);
        
        if (feedbackDiv && form) {
            const isCorrect = result.is_correct;
            const feedback = result.feedback || (isCorrect ? 'Correct!' : 'Incorrect answer.');
            
            feedbackDiv.className = `mt-4 p-3 rounded-lg ${isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            feedbackDiv.innerHTML = `
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${isCorrect ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                        }
                    </svg>
                    <span>${feedback}</span>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <span class="text-sm px-2 py-1 bg-blue-100 text-blue-800 rounded">✓ Sudah Dijawab</span>
                    <button onclick="window.videoPlayer.closeQuiz()" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Lanjutkan Video
                    </button>
                </div>
            `;
            feedbackDiv.classList.remove('hidden');
            form.style.display = 'none';
        }
    }

    skipQuiz(interactionId) {
        this.closeQuiz();
    }

    closeQuiz() {
        const overlay = document.getElementById(`${this.containerId}-quiz-overlay`);
        if (overlay) {
            overlay.classList.add('hidden');
        }
        // Update progress after closing quiz
        this.updateProgressIndicator();
        this.player.playVideo();
    }

    showReflection(interaction) {
        this.player.pauseVideo();
        
        const overlay = document.getElementById(`${this.containerId}-quiz-overlay`);
        const content = document.getElementById('quiz-content');
        
        if (!overlay || !content) return;

        const data = interaction.data || {};
        const reflectionType = data.reflection_type || 'text';
        const question = data.reflection_question || interaction.description || 'Please reflect on this content';
        const isRequired = data.reflection_is_required || false;

        let formContent = '';
        
        if (reflectionType === 'multiple_choice') {
            const options = data.reflection_options || [];
            formContent = `
                <div class="space-y-3">
                    ${options.map((option, index) => `
                        <label class="flex items-center p-3 border rounded-lg hover:bg-purple-50 cursor-pointer">
                            <input type="radio" name="reflection_answer" value="${index}" class="mr-3 text-purple-600">
                            <span>${option.text}</span>
                        </label>
                    `).join('')}
                </div>
            `;
        } else {
            formContent = `
                <textarea name="reflection_text" id="reflection-text-${interaction.id}" 
                          class="w-full p-3 border rounded-lg resize-none focus:border-purple-500 focus:ring-purple-500"
                          rows="4" placeholder="Share your thoughts and reflections here..."
                          ${isRequired ? 'required' : ''}></textarea>
            `;
        }

        content.innerHTML = `
            <div class="text-center mb-4">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-3">
                    <span class="text-purple-600 text-xl">🤔</span>
                </div>
                <h3 class="text-lg font-bold text-gray-800">${interaction.title}</h3>
            </div>
            <p class="text-gray-600 mb-4">${question}</p>
            <form id="reflection-form-${interaction.id}" class="space-y-4">
                ${formContent}
                <div class="flex justify-end space-x-3 mt-6">
                    ${!isRequired ? `
                        <button type="button" onclick="window.videoPlayer.skipReflection(${interaction.id})" 
                                class="px-4 py-2 text-gray-600 border rounded-lg hover:bg-gray-50">
                            Skip
                        </button>
                    ` : ''}
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Submit Reflection
                    </button>
                </div>
            </form>
            <div id="reflection-feedback-${interaction.id}" class="hidden mt-4 p-3 rounded-lg bg-purple-50 text-purple-800"></div>
        `;

        // Add event listener for form submission
        document.getElementById(`reflection-form-${interaction.id}`).addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitReflectionAnswer(interaction, e.target);
        });

        overlay.classList.remove('hidden');
    }

    async submitReflectionAnswer(interaction, form) {
        const formData = new FormData(form);
        const data = interaction.data || {};
        const reflectionType = data.reflection_type || 'text';
        
        let responseData;
        
        if (reflectionType === 'multiple_choice') {
            const selectedAnswer = parseInt(formData.get('reflection_answer'));
            
            if (selectedAnswer === null || selectedAnswer === undefined) {
                this.showNotification('Silakan pilih jawaban terlebih dahulu', 'warning');
                return;
            }
            
            responseData = {
                video_interaction_id: interaction.id,
                response_data: { selected_option: selectedAnswer },
                answered_at: new Date().toISOString()
            };
        } else {
            const reflectionText = formData.get('reflection_text')?.trim();
            
            if (!reflectionText && data.reflection_is_required) {
                this.showNotification('Refleksi wajib diisi', 'warning');
                return;
            }
            
            responseData = {
                video_interaction_id: interaction.id,
                response_data: { reflection_text: reflectionText },
                answered_at: new Date().toISOString()
            };
        }

        try {
            const response = await fetch('/api/video-interactions/response', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(responseData)
            });

            const result = await response.json();

            if (response.ok) {
                this.userResponses[interaction.id] = result;
                this.showReflectionFeedback(interaction, result);
                this.updateProgressIndicator();
                this.showNotification('Refleksi berhasil disimpan!', 'success');
            } else {
                console.error('Failed to submit reflection:', result);
                const errorMessage = result.error || result.message || 'Gagal menyimpan refleksi. Silakan coba lagi.';
                this.showNotification(errorMessage, 'error');
            }
        } catch (error) {
            console.error('Error submitting reflection:', error);
            this.showNotification('Koneksi bermasalah. Periksa koneksi internet Anda.', 'error');
        }
    }

    showReflectionFeedback(interaction, result) {
        const feedbackDiv = document.getElementById(`reflection-feedback-${interaction.id}`);
        const form = document.getElementById(`reflection-form-${interaction.id}`);
        
        if (feedbackDiv && form) {
            const feedback = result.feedback || 'Thank you for your reflection';
            
            feedbackDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-purple-600">✓</span>
                        <span>${feedback}</span>
                    </div>
                    <button onclick="window.videoPlayer.closeReflection()" 
                            class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                        Continue
                    </button>
                </div>
            `;
            feedbackDiv.classList.remove('hidden');
            form.style.display = 'none';
        }
    }

    skipReflection(interactionId) {
        this.closeReflection();
    }

    closeReflection() {
        const overlay = document.getElementById(`${this.containerId}-quiz-overlay`);
        if (overlay) {
            overlay.classList.add('hidden');
        }
        this.updateProgressIndicator();
        this.player.playVideo();
    }

    // Custom notification system (better than alert)
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            type === 'success' ? 'bg-green-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <span>${message}</span>
                <button onclick="this.parentNode.parentNode.remove()" class="ml-2 text-white hover:text-gray-200">×</button>
            </div>
        `;
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Update progress indicator real-time
    updateProgressIndicator() {
        const totalInteractions = this.interactions.length;
        const completedInteractions = Object.keys(this.userResponses).length;
        const completionPercentage = totalInteractions > 0 ? Math.round((completedInteractions / totalInteractions) * 100) : 0;

        // Update the progress display
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        const interactionCount = document.getElementById('interaction-count');

        if (progressBar) {
            progressBar.style.width = completionPercentage + '%';
        }
        if (progressPercentage) {
            progressPercentage.textContent = completionPercentage + '%';
        }
        
        // Only log in development mode
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            console.log(`Progress updated: ${completedInteractions}/${totalInteractions} = ${completionPercentage}%`);
        }
    }

    showPause(interaction) {
        this.player.pauseVideo();
        this.showInteractionPopup(interaction, 'pause', `
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">${interaction.title}</h3>
                <p class="text-gray-600 mb-4">${interaction.description || 'Video paused'}</p>
                <button onclick="window.videoPlayer.closeInteractionPopup()" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Continue Video
                </button>
            </div>
        `);
    }

    showOverlay(interaction) {
        const duration = interaction.data?.duration || 5;
        const content = interaction.data?.overlay_content || interaction.description;
        
        this.showInteractionPopup(interaction, 'overlay', `
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">${interaction.title}</h3>
                <div class="text-gray-600 mb-4">${content}</div>
                <div class="text-sm text-gray-500 mb-4">This overlay will auto-close in <span id="overlay-countdown">${duration}</span> seconds</div>
                <button onclick="window.videoPlayer.closeInteractionPopup()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Close Now
                </button>
            </div>
        `);

        // Auto close after duration
        let countdown = duration;
        const countdownElement = document.getElementById('overlay-countdown');
        const countdownInterval = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                this.closeInteractionPopup();
            }
        }, 1000);
    }

    showAnnotation(interaction) {
        const annotationText = interaction.data?.annotation_text || interaction.description;
        
        this.showInteractionPopup(interaction, 'annotation', `
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v10a2 2 0 002 2h8a2 2 0 002-2V8M9 12h6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">${interaction.title}</h3>
                <div class="text-gray-600 mb-4 text-left bg-yellow-50 p-4 rounded-lg border border-yellow-200">${annotationText}</div>
                <button onclick="window.videoPlayer.closeInteractionPopup()" 
                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Got it!
                </button>
            </div>
        `);
    }

    showHotspot(interaction) {
        const position = interaction.data?.position || {x: 50, y: 25};
        
        // Create hotspot indicator on video
        const hotspotIndicator = document.createElement('div');
        hotspotIndicator.className = 'absolute bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer z-10 animate-pulse hover:scale-110 transition-transform';
        hotspotIndicator.style.left = position.x + '%';
        hotspotIndicator.style.top = position.y + '%';
        hotspotIndicator.innerHTML = '<span class="text-sm font-bold">!</span>';
        hotspotIndicator.id = `hotspot-${interaction.id}`;
        
        // Add click handler
        hotspotIndicator.onclick = () => {
            this.showInteractionPopup(interaction, 'hotspot', `
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">${interaction.title}</h3>
                    <p class="text-gray-600 mb-4">${interaction.description || 'Important information about this part of the video'}</p>
                    <button onclick="window.videoPlayer.closeInteractionPopup()" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Close
                    </button>
                </div>
            `);
            // Remove hotspot after clicking
            hotspotIndicator.remove();
        };
        
        // Add to video container
        const videoContainer = document.querySelector(`#${this.containerId} .relative`);
        if (videoContainer) {
            videoContainer.appendChild(hotspotIndicator);
            
            // Remove hotspot after 10 seconds if not clicked
            setTimeout(() => {
                if (document.getElementById(`hotspot-${interaction.id}`)) {
                    hotspotIndicator.remove();
                }
            }, 10000);
        }
    }

    showInteractionPopup(interaction, type, content) {
        // Pause video for most interaction types (except overlay which can continue playing)
        if (type !== 'overlay') {
            this.player.pauseVideo();
        }
        
        const overlay = document.getElementById(`${this.containerId}-quiz-overlay`);
        const contentDiv = document.getElementById('quiz-content');
        
        if (!overlay || !contentDiv) return;

        contentDiv.innerHTML = content;
        overlay.classList.remove('hidden');
    }

    closeInteractionPopup() {
        const overlay = document.getElementById(`${this.containerId}-quiz-overlay`);
        if (overlay) {
            overlay.classList.add('hidden');
        }
        this.updateProgressIndicator();
        this.player.playVideo();
    }

    createInteractionOverlay() {
        return `
            <div class="absolute top-2 right-2 bg-black bg-opacity-70 text-white px-3 py-2 rounded-lg text-sm">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>${this.interactions.length} Interaksi</span>
                </div>
            </div>
        `;
    }

    showInteractionInfo() {
        // Only log in development mode
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            console.log(`Interactive video loaded for content ${this.contentId}`);
            console.log('Interactions:', this.interactions);
        }
        
        // Update the interaction count in the UI if element exists
        const countElement = document.getElementById('interaction-count');
        if (countElement) {
            countElement.textContent = this.interactions.length;
        }
        
            // Show interaction details in development mode only
            if (this.interactions.length > 0) {
                console.log('Interaction timestamps:');
                this.interactions.forEach(interaction => {
                    const minutes = Math.floor(interaction.timestamp / 60);
                    const seconds = Math.floor(interaction.timestamp % 60);
                    console.log(`- ${interaction.title} at ${minutes}:${seconds.toString().padStart(2, '0')} (${interaction.type})`);
                });
        }
    }

    // Simple method to get interaction stats (called from external scripts if needed)
    getStats() {
        return {
            total: this.interactions.length,
            byType: this.interactions.reduce((acc, interaction) => {
                acc[interaction.type] = (acc[interaction.type] || 0) + 1;
                return acc;
            }, {}),
            timestamps: this.interactions.map(i => ({
                type: i.type,
                timestamp: i.timestamp,
                title: i.title
            }))
        };
    }

    destroy() {
        this.stopTimeTracking();
        if (this.player && this.player.destroy) {
            this.player.destroy();
        }
        // Only log in development mode
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            console.log('Interactive video player destroyed');
        }
    }
}

// Export for global use
window.InteractiveVideoPlayer = InteractiveVideoPlayer;