<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-900 to-red-700 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    File Manager
                </h2>
                <p class="text-sm text-gray-600 mt-1">Manage all your uploaded files in one place</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs text-gray-500">Total Files</div>
                    <div class="text-2xl font-bold text-gray-900" id="fileCount">0</div>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
                    <div class="text-xs text-gray-500">Total Size</div>
                    <div class="text-2xl font-bold text-gray-900" id="totalSize">0 MB</div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Upload Section with Drag & Drop --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="p-6">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-200 hover:border-red-500 hover:bg-red-50 cursor-pointer">
                            <input type="file" id="fileInput" name="file" class="hidden" multiple>

                            <div id="dropZoneContent">
                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Drop files here or click to upload</h3>
                                <p class="text-sm text-gray-500 mb-4">Support all file types • Maximum 20MB per file</p>
                                <button type="button" onclick="document.getElementById('fileInput').click()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-900 to-red-700 text-white font-medium rounded-lg hover:from-red-800 hover:to-red-600 transition-all duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Choose Files
                                </button>
                            </div>

                            <div id="uploadProgress" class="hidden">
                                <div class="mb-3">
                                    <svg class="animate-spin mx-auto h-12 w-12 text-red-900" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mb-2">Uploading <span id="uploadCount">0</span> of <span id="uploadTotal">0</span> files...</p>
                                <div id="uploadProgressList" class="space-y-2 max-w-md mx-auto"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Filter & Search Bar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Search files by name..." class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-150 ease-in-out sm:text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-2 gap-4 lg:w-auto">
                            <select id="typeFilter" class="block w-full px-4 py-3 pr-8 border border-gray-300 bg-white rounded-lg leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-150 ease-in-out">
                                <option value="">All Types</option>
                                <option value="image">📷 Images</option>
                                <option value="document">📄 Documents</option>
                                <option value="archive">📦 Archives</option>
                                <option value="video">🎬 Videos</option>
                                <option value="other">📎 Others</option>
                            </select>
                            <select id="sortBy" class="block w-full px-4 py-3 pr-8 border border-gray-300 bg-white rounded-lg leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-150 ease-in-out">
                                <option value="date-desc">🕒 Newest First</option>
                                <option value="date-asc">🕐 Oldest First</option>
                                <option value="name-asc">🔤 Name (A-Z)</option>
                                <option value="name-desc">🔡 Name (Z-A)</option>
                                <option value="size-desc">📊 Largest First</option>
                                <option value="size-asc">📉 Smallest First</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Files Grid --}}
            <div id="filesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {{-- Files will be loaded here via JavaScript --}}
            </div>

            {{-- Empty State --}}
            <div id="emptyState" class="hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No files found</h3>
                    <p class="text-gray-500 mb-6">Upload your first file to get started</p>
                    <button onclick="document.getElementById('fileInput').click()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-900 to-red-700 text-white font-medium rounded-lg hover:from-red-800 hover:to-red-600 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Upload File
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="toast" class="fixed bottom-4 right-4 bg-white rounded-lg shadow-2xl border border-gray-200 p-4 transform transition-all duration-300 translate-y-20 opacity-0 z-50" style="min-width: 300px;">
        <div class="flex items-start gap-3">
            <div id="toastIcon" class="flex-shrink-0"></div>
            <div class="flex-1">
                <p id="toastMessage" class="text-sm font-medium text-gray-900"></p>
            </div>
            <button onclick="hideToast()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>

    @push('styles')
    <style>
        .file-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .file-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.15);
        }
        .file-preview {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        #dropZone.drag-over {
            border-color: #7f1d1d;
            background-color: #fef2f2;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        let allFiles = @json($files);
        let filteredFiles = [...allFiles];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            displayFiles(filteredFiles);
            updateStats();
            setupDragAndDrop();

            // Event listeners
            document.getElementById('searchInput').addEventListener('input', debounce(filterFiles, 300));
            document.getElementById('typeFilter').addEventListener('change', filterFiles);
            document.getElementById('sortBy').addEventListener('change', sortFiles);
            document.getElementById('fileInput').addEventListener('change', handleFileSelect);
        });

        // Setup Drag and Drop
        function setupDragAndDrop() {
            const dropZone = document.getElementById('dropZone');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.add('drag-over');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.remove('drag-over');
                }, false);
            });

            dropZone.addEventListener('drop', handleDrop, false);
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('fileInput').files = files;
            handleFileSelect();
        }

        // Display files
        function displayFiles(files) {
            const grid = document.getElementById('filesGrid');
            const emptyState = document.getElementById('emptyState');

            if (files.length === 0) {
                grid.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            grid.classList.remove('hidden');
            emptyState.classList.add('hidden');

            grid.innerHTML = files.map(file => `
                <div class="file-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:border-red-300" data-file-id="${file.path}">
                    <div class="file-preview aspect-w-16 aspect-h-12 flex items-center justify-center p-6">
                        ${getFilePreview(file)}
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-900 truncate mb-1" title="${file.name}">${file.name}</h4>
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                ${formatFileSize(file.size)}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                ${formatDate(file.modified)}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="copyLink('${file.url}', '${file.name}')" class="flex-1 group relative px-3 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 text-xs font-medium flex items-center justify-center gap-2 shadow-sm hover:shadow">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copy Link
                            </button>
                            <button onclick="deleteFile('${file.path}', '${file.name}')" class="flex-shrink-0 px-3 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Get file preview
        function getFilePreview(file) {
            if (file.type === 'image') {
                return `<img src="${file.url}" alt="${file.name}" class="max-w-full max-h-full object-contain rounded-lg">`;
            }

            const icons = {
                'document': `<div class="text-blue-600"><svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg></div>`,
                'archive': `<div class="text-yellow-600"><svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg></div>`,
                'video': `<div class="text-purple-600"><svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path></svg></div>`,
                'other': `<div class="text-gray-500"><svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg></div>`
            };

            return icons[file.type] || icons.other;
        }

        // Copy link with better feedback
        function copyLink(url, filename) {
            const fullUrl = window.location.origin + url;

            navigator.clipboard.writeText(fullUrl).then(() => {
                showToast(`Link copied: ${filename}`, 'success');
            }).catch(() => {
                // Fallback
                const textArea = document.createElement('textarea');
                textArea.value = fullUrl;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast(`Link copied: ${filename}`, 'success');
                } catch (err) {
                    showToast('Failed to copy link', 'error');
                }
                document.body.removeChild(textArea);
            });
        }

        // Delete file
        function deleteFile(path, name) {
            if (!confirm(`Delete "${name}"?\n\nThis action cannot be undone.`)) {
                return;
            }

            showToast('Deleting file...', 'info');

            fetch('{{ route('file-control.delete') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ path: path })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('File deleted successfully', 'success');
                    allFiles = allFiles.filter(f => f.path !== path);
                    filterFiles();
                } else {
                    showToast(data.message || 'Delete failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Delete failed', 'error');
            });
        }

        // Handle file select - Multiple Files
        async function handleFileSelect() {
            const files = Array.from(document.getElementById('fileInput').files);
            if (files.length === 0) return;

            // Show upload progress
            document.getElementById('dropZoneContent').classList.add('hidden');
            document.getElementById('uploadProgress').classList.remove('hidden');
            document.getElementById('uploadTotal').textContent = files.length;
            document.getElementById('uploadCount').textContent = 0;

            const progressList = document.getElementById('uploadProgressList');
            progressList.innerHTML = '';

            // Create progress items for each file
            const progressItems = files.map((file, index) => {
                const item = document.createElement('div');
                item.className = 'bg-gray-100 rounded-lg p-3';
                item.innerHTML = `
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-700 truncate flex-1 mr-2" title="${file.name}">${file.name}</span>
                        <span class="text-xs text-gray-500" id="fileSize-${index}">${formatFileSize(file.size)}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-gradient-to-r from-red-900 to-red-700 h-1.5 rounded-full transition-all duration-300" style="width: 0%" id="progress-${index}"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1" id="status-${index}">Waiting...</div>
                `;
                progressList.appendChild(item);
                return {
                    progressBar: item.querySelector(`#progress-${index}`),
                    status: item.querySelector(`#status-${index}`)
                };
            });

            let successCount = 0;
            let failCount = 0;

            // Upload files one by one
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const { progressBar, status } = progressItems[i];

                try {
                    status.textContent = 'Uploading...';
                    status.className = 'text-xs text-blue-600 mt-1 font-medium';

                    // Simulate progress
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += 10;
                        progressBar.style.width = Math.min(progress, 90) + '%';
                    }, 100);

                    // Upload file
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    const response = await fetch('{{ route('file-control.upload') }}', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    clearInterval(interval);
                    progressBar.style.width = '100%';

                    if (data.success) {
                        successCount++;
                        status.textContent = '✓ Uploaded';
                        status.className = 'text-xs text-green-600 mt-1 font-medium';
                        progressBar.classList.remove('from-red-900', 'to-red-700');
                        progressBar.classList.add('from-green-600', 'to-green-700');
                    } else {
                        failCount++;
                        status.textContent = '✗ Failed: ' + (data.message || 'Unknown error');
                        status.className = 'text-xs text-red-600 mt-1 font-medium';
                        progressBar.classList.remove('from-red-900', 'to-red-700');
                        progressBar.classList.add('from-red-600', 'to-red-700');
                    }
                } catch (error) {
                    failCount++;
                    console.error('Error uploading file:', file.name, error);
                    status.textContent = '✗ Upload failed';
                    status.className = 'text-xs text-red-600 mt-1 font-medium';
                    progressBar.classList.remove('from-red-900', 'to-red-700');
                    progressBar.classList.add('from-red-600', 'to-red-700');
                }

                // Update count
                document.getElementById('uploadCount').textContent = i + 1;
            }

            // Show summary
            setTimeout(() => {
                if (successCount > 0) {
                    showToast(`${successCount} file(s) uploaded successfully${failCount > 0 ? `, ${failCount} failed` : ''}!`, successCount === files.length ? 'success' : 'info');
                } else {
                    showToast('All uploads failed', 'error');
                }

                // Reload after 2 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }, 500);
        }

        function resetUploadForm() {
            document.getElementById('dropZoneContent').classList.remove('hidden');
            document.getElementById('uploadProgress').classList.add('hidden');
            document.getElementById('uploadProgressList').innerHTML = '';
            document.getElementById('fileInput').value = '';
        }

        // Filter files
        function filterFiles() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;

            filteredFiles = allFiles.filter(file => {
                const matchSearch = file.name.toLowerCase().includes(searchTerm);
                const matchType = !typeFilter || file.type === typeFilter;
                return matchSearch && matchType;
            });

            sortFiles();
        }

        // Sort files
        function sortFiles() {
            const sortBy = document.getElementById('sortBy').value;

            switch(sortBy) {
                case 'date-asc':
                    filteredFiles.sort((a, b) => a.modified - b.modified);
                    break;
                case 'date-desc':
                    filteredFiles.sort((a, b) => b.modified - a.modified);
                    break;
                case 'name-asc':
                    filteredFiles.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'name-desc':
                    filteredFiles.sort((a, b) => b.name.localeCompare(a.name));
                    break;
                case 'size-asc':
                    filteredFiles.sort((a, b) => a.size - b.size);
                    break;
                case 'size-desc':
                    filteredFiles.sort((a, b) => b.size - a.size);
                    break;
            }

            displayFiles(filteredFiles);
            updateStats();
        }

        // Update stats
        function updateStats() {
            document.getElementById('fileCount').textContent = filteredFiles.length;
            const totalBytes = filteredFiles.reduce((sum, file) => sum + file.size, 0);
            document.getElementById('totalSize').textContent = formatFileSize(totalBytes);
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Format date
        function formatDate(timestamp) {
            const date = new Date(timestamp * 1000);
            const now = new Date();
            const diff = now - date;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));

            if (days === 0) return 'Today';
            if (days === 1) return 'Yesterday';
            if (days < 7) return days + 'd ago';
            if (days < 30) return Math.floor(days / 7) + 'w ago';
            if (days < 365) return Math.floor(days / 30) + 'mo ago';

            return Math.floor(days / 365) + 'y ago';
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');

            const icons = {
                success: '<svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                error: '<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
                info: '<svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
            };

            toastIcon.innerHTML = icons[type] || icons.info;
            toastMessage.textContent = message;

            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                hideToast();
            }, 3000);
        }

        function hideToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('translate-y-20', 'opacity-0');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
    @endpush
</x-app-layout>
