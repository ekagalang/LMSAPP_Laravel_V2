<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <!-- Floating Header -->
        <div class="sticky top-0 z-40 backdrop-blur-lg bg-white/70 dark:bg-gray-900/70 border-b border-gray-200/20 dark:border-gray-700/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Certificate Verification</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Secure digital certificate validation</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm font-medium">
                            ✓ Verified
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                
                <!-- Certificate Details Sidebar -->
                <div class="xl:col-span-4 space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-5">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Verified Certificate</h3>
                                    <p class="text-emerald-100 text-sm">Authentic and valid</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-5">
                                <!-- Recipient -->
                                <div class="group">
                                    <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Certificate Holder</label>
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 group-hover:shadow-md transition-all duration-300">
                                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $certificate->user->name }}</p>
                                    </div>
                                </div>

                                <!-- Course -->
                                <div class="group">
                                    <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Course Title</label>
                                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 group-hover:shadow-md transition-all duration-300">
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $certificate->course->title }}</p>
                                    </div>
                                </div>

                                <!-- Date & ID Grid -->
                                    <div class="group">
                                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Issue Date</label>
                                        <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 group-hover:shadow-md transition-all duration-300">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $certificate->issued_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 block">Cert ID</label>
                                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 group-hover:shadow-md transition-all duration-300">
                                            <p class="text-xs font-mono font-bold text-gray-900 dark:text-white break-all">{{ ($certificate->certificate_code) }}</p>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Actions</h4>
                        <div class="space-y-3">
                            <a href="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}" 
                               download 
                               class="group w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-1">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Certificate
                            </a>
                            
                            <button onclick="shareDocument()" class="group w-full inline-flex items-center justify-center px-6 py-4 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-300 transform hover:-translate-y-1">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                </svg>
                                Share Certificate
                            </button>

                            <button onclick="openInNewTab()" class="group w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-1">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Open in New Tab
                            </button>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-3xl p-6 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-500 rounded-2xl flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-2">Secure Verification</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-400 leading-relaxed">This certificate has been cryptographically verified and stored securely with blockchain-level authenticity.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Viewer Main Area -->
                <div class="xl:col-span-8">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        
                        <!-- PDF Controls Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Certificate Preview</h3>
                                    <div class="hidden sm:flex items-center space-x-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Live Preview</span>
                                    </div>
                                </div>
                                
                                <!-- PDF Navigation -->
                                <div id="pdf-controls" class="hidden flex items-center space-x-3">
                                    <div class="flex items-center bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-600 overflow-hidden">
                                        <button id="prev-page" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        <div class="px-4 py-2 border-x border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <span id="page-num">1</span> / <span id="page-count">-</span>
                                            </span>
                                        </div>
                                        <button id="next-page" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Zoom Controls -->
                                    <div class="flex items-center bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-600 overflow-hidden">
                                        <button id="zoom-out" class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <div class="px-3 py-2 border-x border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                            <span id="zoom-level" class="text-sm font-medium text-gray-700 dark:text-gray-300">100%</span>
                                        </div>
                                        <button id="zoom-in" class="px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="pdf-loading" class="flex items-center justify-center h-96 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700">
                            <div class="text-center">
                                <div class="relative">
                                    <div class="w-20 h-20 border-4 border-blue-200 dark:border-blue-700 rounded-full animate-spin border-t-blue-600 dark:border-t-blue-400 mx-auto mb-4"></div>
                                    <div class="absolute inset-0 w-20 h-20 border-4 border-transparent rounded-full animate-ping border-t-blue-400 mx-auto"></div>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Loading Certificate</h3>
                                <p class="text-gray-500 dark:text-gray-400">Preparing secure document viewer...</p>
                            </div>
                        </div>

                        <!-- PDF Viewer Container -->
                        <div id="pdf-viewer" class="hidden">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 p-6" style="min-height: 800px;">
                                <div class="flex justify-center">
                                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                                        <canvas id="pdf-canvas" class="max-w-full h-auto"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fallback State -->
                        <div id="pdf-fallback" class="hidden flex flex-col items-center justify-center h-96 p-8 text-center bg-gradient-to-br from-red-50 to-pink-50 dark:from-gray-800 dark:to-gray-700">
                            <div class="w-24 h-24 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-12 h-12 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-3">Preview Unavailable</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md">The certificate preview cannot be displayed in this browser. You can still download or open it in a new tab.</p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Open in New Tab
                                </a>
                                <a href="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}" 
                                   download
                                   class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Load PDF.js from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <script>
        // Configure PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');
        const pdfUrl = "{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}";

        // Render halaman PDF dengan animasi
        function renderPage(num) {
            pageRendering = true;
            
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({scale: scale});
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                canvas.style.opacity = '0.5';
                canvas.style.transition = 'opacity 0.3s ease';

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                const renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;
                    canvas.style.opacity = '1';
                    
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });

            document.getElementById('page-num').textContent = num;
        }

        // Queue render page
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        // Navigation functions
        function onPrevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
            updateButtons();
        }

        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
            updateButtons();
        }

        function updateButtons() {
            document.getElementById('prev-page').disabled = (pageNum <= 1);
            document.getElementById('next-page').disabled = (pageNum >= pdfDoc.numPages);
        }

        function zoomIn() {
            if (scale < 3.0) {
                scale += 0.25;
                queueRenderPage(pageNum);
                document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
            }
        }

        function zoomOut() {
            if (scale > 0.5) {
                scale -= 0.25;
                queueRenderPage(pageNum);
                document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
            }
        }

        function showFallback() {
            document.getElementById('pdf-loading').classList.add('hidden');
            document.getElementById('pdf-viewer').classList.add('hidden');
            document.getElementById('pdf-fallback').classList.remove('hidden');
        }

        // Utility functions
        function shareDocument() {
            if (navigator.share) {
                navigator.share({
                    title: 'Certificate Verification',
                    text: 'Check out this verified certificate!',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showToast('Certificate link copied to clipboard!');
                });
            }
        }

        function openInNewTab() {
            window.open("{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}", '_blank');
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-2xl shadow-lg transform translate-x-full transition-transform duration-300';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.style.transform = 'translateX(0)', 100);
            setTimeout(() => {
                toast.style.transform = 'translateX(full)';
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }

        // Load PDF
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('page-count').textContent = pdfDoc.numPages;

            setTimeout(() => {
                document.getElementById('pdf-loading').classList.add('hidden');
                document.getElementById('pdf-viewer').classList.remove('hidden');
                document.getElementById('pdf-controls').classList.remove('hidden');
                document.getElementById('pdf-controls').classList.add('flex');

                renderPage(pageNum);
                updateButtons();
                
                console.log('PDF loaded successfully');
            }, 1000);
            
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            setTimeout(() => showFallback(), 1000);
        });

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('prev-page').addEventListener('click', onPrevPage);
            document.getElementById('next-page').addEventListener('click', onNextPage);
            document.getElementById('zoom-in').addEventListener('click', zoomIn);
            document.getElementById('zoom-out').addEventListener('click', zoomOut);

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                    e.preventDefault();
                    onPrevPage();
                } else if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                    e.preventDefault();
                    onNextPage();
                } else if (e.key === '+' || e.key === '=') {
                    e.preventDefault();
                    zoomIn();
                } else if (e.key === '-') {
                    e.preventDefault();
                    zoomOut();
                }
            });

            // Mouse wheel zoom
            canvas.addEventListener('wheel', function(e) {
                e.preventDefault();
                if (e.deltaY < 0) {
                    zoomIn();
                } else {
                    zoomOut();
                }
            });

            // Mobile touch gestures
            let startDistance = 0;
            let startScale = scale;

            canvas.addEventListener('touchstart', function(e) {
                if (e.touches.length === 2) {
                    startDistance = Math.hypot(
                        e.touches[0].pageX - e.touches[1].pageX,
                        e.touches[0].pageY - e.touches[1].pageY
                    );
                    startScale = scale;
                }
            });

            canvas.addEventListener('touchmove', function(e) {
                if (e.touches.length === 2) {
                    e.preventDefault();
                    const currentDistance = Math.hypot(
                        e.touches[0].pageX - e.touches[1].pageX,
                        e.touches[0].pageY - e.touches[1].pageY
                    );
                    const newScale = startScale * (currentDistance / startDistance);
                    
                    if (newScale >= 0.5 && newScale <= 3.0) {
                        scale = newScale;
                        queueRenderPage(pageNum);
                        document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
                    }
                }
            });

            // Add entrance animations
            const animatedElements = document.querySelectorAll('.xl\\:col-span-4 > *, .xl\\:col-span-8 > *');
            animatedElements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 200);
            });

            // Add floating animation to verification badge
            const badge = document.querySelector('.sticky .bg-green-100');
            if (badge) {
                badge.style.animation = 'float 3s ease-in-out infinite';
            }
        });

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-5px); }
            }
            
            @keyframes glow {
                0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
                50% { box-shadow: 0 0 30px rgba(59, 130, 246, 0.8), 0 0 40px rgba(59, 130, 246, 0.3); }
            }
            
            .group:hover .bg-gradient-to-r {
                animation: glow 2s ease-in-out infinite;
            }
            
            #pdf-canvas {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            #pdf-canvas:hover {
                transform: scale(1.02);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            }
            
            .backdrop-blur-lg {
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
            }
            
            /* Custom scrollbar */
            * {
                scrollbar-width: thin;
                scrollbar-color: rgba(59, 130, 246, 0.3) transparent;
            }
            
            *::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            
            *::-webkit-scrollbar-track {
                background: transparent;
            }
            
            *::-webkit-scrollbar-thumb {
                background-color: rgba(59, 130, 246, 0.3);
                border-radius: 20px;
                border: transparent;
            }
            
            *::-webkit-scrollbar-thumb:hover {
                background-color: rgba(59, 130, 246, 0.5);
            }
            
            /* Smooth transitions for all interactive elements */
            button, a, .group {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Enhanced button hover effects */
            button:hover:not(:disabled) {
                transform: translateY(-2px);
            }
            
            button:active:not(:disabled) {
                transform: translateY(0);
            }
            
            /* Loading spinner enhancement */
            @keyframes pulse-glow {
                0%, 100% {
                    opacity: 1;
                    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
                }
                50% {
                    opacity: 0.8;
                    box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
                }
            }
            
            .animate-ping {
                animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>