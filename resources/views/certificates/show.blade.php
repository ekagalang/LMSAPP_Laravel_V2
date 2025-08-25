    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Certificate Verification') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="md:col-span-1">
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Certificate Details</h3>
                                <div class="mt-4 space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="font-semibold text-gray-600 dark:text-gray-400">Recipient:</span>
                                        <span class="text-right">{{ $certificate->user->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold text-gray-600 dark:text-gray-400">Course:</span>
                                        <span class="text-right">{{ $certificate->course->title }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold text-gray-600 dark:text-gray-400">Issued On:</span>
                                        <span class="text-right">{{ $certificate->issued_at->format('d F Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold text-gray-600 dark:text-gray-400">Certificate ID:</span>
                                        <span class="text-right font-mono text-xs">{{ $certificate->certificate_code }}</span>
                                    </div>
                                </div>
                                <div class="mt-6 border-t pt-4 text-center">
                                    <p class="text-lg font-semibold text-green-600 dark:text-green-400 flex items-center justify-center">
                                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        STATUS: VALID
                                    </p>
                                </div>
                                <div class="mt-6">
                                    <a href="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}" download class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition">
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <div class="w-full h-full min-h-[600px] bg-gray-200 dark:bg-gray-900 rounded-lg shadow-inner relative">
                                    <!-- PDF Preview dengan fallback -->
                                    <div id="pdf-container" class="w-full h-full">
                                        <!-- Coba iframe dulu -->
                                        <iframe 
                                            id="pdf-iframe"
                                            src="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}#toolbar=0&navpanes=0" 
                                            width="100%" 
                                            height="800px" 
                                            class="rounded-lg"
                                            onload="pdfLoaded()"
                                            onerror="showFallback()">
                                        </iframe>
                                        
                                        <!-- Fallback jika iframe gagal -->
                                        <div id="pdf-fallback" class="hidden w-full h-full flex flex-col items-center justify-center p-8 text-center">
                                            <svg class="w-24 h-24 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                Certificate Preview
                                            </h3>
                                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                                Certificate preview tidak dapat ditampilkan di browser ini.
                                            </p>
                                            <div class="space-y-3">
                                                <a href="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                    Buka di Tab Baru
                                                </a>
                                                <a href="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}" 
                                                   download
                                                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                </div>
            </div>
        </div>
    </x-app-layout>

    <script>
    let iframeLoaded = false;
    
    function pdfLoaded() {
        iframeLoaded = true;
        console.log('PDF loaded successfully in iframe');
    }
    
    function showFallback() {
        console.log('PDF iframe failed to load, showing fallback');
        document.getElementById('pdf-iframe').style.display = 'none';
        document.getElementById('pdf-fallback').classList.remove('hidden');
    }
    
    // Deteksi jika iframe gagal load setelah 5 detik
    setTimeout(function() {
        if (!iframeLoaded) {
            console.log('PDF iframe timeout, showing fallback');
            showFallback();
        }
    }, 5000);
    
    // Deteksi X-Frame-Options error
    window.addEventListener('message', function(event) {
        if (event.data === 'iframe-blocked') {
            showFallback();
        }
    });
    
    // Alternative detection method untuk X-Frame-Options
    document.addEventListener('DOMContentLoaded', function() {
        const iframe = document.getElementById('pdf-iframe');
        
        iframe.addEventListener('load', function() {
            try {
                // Coba akses iframe content untuk deteksi blocking
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (!iframeDoc) {
                    throw new Error('Cannot access iframe content');
                }
                iframeLoaded = true;
            } catch (e) {
                console.log('Iframe blocked:', e.message);
                showFallback();
            }
        });
        
        iframe.addEventListener('error', function() {
            console.log('Iframe error event fired');
            showFallback();
        });
    });
    </script>
    