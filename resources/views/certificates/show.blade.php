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
                                <div class="w-full h-full min-h-[600px] bg-gray-200 dark:bg-gray-900 rounded-lg shadow-inner">
                                    <iframe src="{{ Storage::url('certificates/' . $certificate->certificate_code . '.pdf') }}#toolbar=0&navpanes=0" width="100%" height="800px" class="rounded-lg"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
    