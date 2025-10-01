<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Detail Pengumuman
                </h2>
                <p class="text-sm text-gray-600 mt-1">Lihat detail pengumuman yang dipilih</p>
            </div>
            <a href="javascript:void(0)" onclick="window.history.back()"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 border border-gray-300 rounded-xl font-semibold text-sm text-gray-700 uppercase tracking-wide transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Content Card -->
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
                <!-- Header Section with Gradient -->
                <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-3 leading-tight">
                                {{ $announcement->title }}
                            </h1>
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8l-4-4m0 0V7a4 4 0 118 0v8m-4-4l4-4"></path>
                                    </svg>
                                    <span class="font-medium">Dipublikasikan pada</span>
                                </div>
                                <span class="bg-white px-3 py-1 rounded-full text-xs font-semibold text-gray-700 shadow-sm">
                                    {{ $announcement->created_at->format('d F Y, H:i') }}
                                </span>
                            </div>
                        </div>
                        <!-- Status Badge (jika ada field status) -->
                        <div class="ml-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="px-8 py-8">
                    <div class="prose prose-lg max-w-none">
                        <!-- Custom styling untuk content -->
                        <style>
                            .prose {
                                color: #374151;
                                line-height: 1.7;
                            }
                            .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
                                color: #1f2937;
                                font-weight: 700;
                                margin-top: 2rem;
                                margin-bottom: 1rem;
                            }
                            .prose p {
                                margin-bottom: 1.5rem;
                                text-align: justify;
                            }
                            .prose ul, .prose ol {
                                margin: 1.5rem 0;
                                padding-left: 1.5rem;
                            }
                            .prose li {
                                margin-bottom: 0.5rem;
                            }
                            .prose blockquote {
                                border-left: 4px solid #3b82f6;
                                background: #f8fafc;
                                padding: 1rem 1.5rem;
                                margin: 2rem 0;
                                font-style: italic;
                            }
                            .prose img {
                                border-radius: 0.75rem;
                                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
                                margin: 2rem auto;
                            }
                        </style>
                        {!! $announcement->content !!}
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Terakhir diperbarui: {{ $announcement->updated_at->format('d F Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>