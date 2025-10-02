<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <!-- Header Section -->
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-10"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-medium mb-4">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Certificate Verification
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                        Verify Your Certificate
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Authentic digital certificate with blockchain-level security verification
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 -mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Certificate Details Card -->
                <div class="lg:col-span-4">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <!-- Status Banner -->
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                            <div class="flex items-center justify-center text-white">
                                <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h3 class="text-xl font-bold">VERIFIED</h3>
                                    <p class="text-green-100 text-sm">Certificate is authentic</p>
                                </div>
                            </div>
                        </div>

                        <!-- Certificate Information -->
                        <div class="p-6 space-y-6">
                            <div class="text-center border-b border-gray-200 dark:border-gray-700 pb-6">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mx-auto flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Certificate Details</h3>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Recipient</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $certificate->user->name }}</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Course</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $certificate->course->title }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Issued Date</label>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ $certificate->issued_at->format('d M Y') }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Certificate ID</label>
                                        <p class="text-xs font-mono font-semibold text-gray-900 dark:text-white mt-1 break-all">{{ $certificate->certificate_code }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="pt-6 space-y-3">
                                <a href="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}" 
                                   download 
                                   class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download Certificate
                                </a>
                                
                                <button onclick="shareDocument()" class="w-full inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                    Share Certificate
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Info -->
                    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300">Secure Verification</h4>
                                <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">This certificate has been cryptographically verified and is stored securely in our system.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Viewer -->
                <div class="lg:col-span-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Certificate Preview</h3>
                                <div class="flex items-center space-x-2">
                                    <button class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </button>
                                    <button class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative">
                            <div class="absolute inset-0 bg-gray-100 dark:bg-gray-900 animate-pulse" id="pdf-loader">
                                <div class="flex items-center justify-center h-full">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                                            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400">Loading certificate...</p>
                                    </div>
                                </div>
                            </div>
                            <iframe 
                                src="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH" 
                                width="100%" 
                                height="800px" 
                                class="w-full"
                                onload="document.getElementById('pdf-loader').style.display='none'"
                                style="border: none;">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function shareDocument() {
            if (navigator.share) {
                navigator.share({
                    title: 'Certificate Verification',
                    text: 'Check out my verified certificate!',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Certificate link copied to clipboard!');
                });
            }
        }

        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.bg-white, .bg-gray-50');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</x-app-layout>