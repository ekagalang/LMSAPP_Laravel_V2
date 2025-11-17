<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 -mx-4 -my-2 px-4 py-8 sm:px-6 lg:px-8 rounded-2xl shadow-lg">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-white text-3xl font-bold leading-tight">
                        {{ __('Manajemen Sertifikat') }}
                    </h2>
                    <p class="text-indigo-100 mt-2">
                        {{ __('Kelola semua sertifikat peserta berdasarkan kursus') }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Actions -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div class="flex space-x-4">
                    <a href="{{ route('certificate-management.analytics') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        📊 Analytics
                    </a>
                </div>
            </div>
        </div>

        <!-- Analytics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">📜</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Sertifikat</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['total_certificates']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">📅</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Bulan Ini</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['certificates_this_month']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">🎓</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Kursus Aktif</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($analytics['courses_with_certificates']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <span class="text-white font-bold">⏱️</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Terbaru</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $analytics['recent_certificates']->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('certificate-management.index') }}" class="flex flex-wrap gap-4">
                    <!-- Search by Name -->
                    <div class="flex-1 min-w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nama peserta..." 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    
                    <!-- Filter by Course -->
                    <div class="flex-1 min-w-64">
                        <select name="course_id" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Semua Kursus</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} ({{ $course->certificates_count }} sertifikat)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Cari
                        </button>
                        <a href="{{ route('certificate-management.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Course Quick Navigation -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Navigasi Cepat Berdasarkan Kursus</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($courses->take(8) as $course)
                        <a href="{{ route('certificate-management.by-course', $course) }}" 
                           class="block p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-150 ease-in-out">
                            <div class="font-medium text-gray-900 truncate">{{ $course->title }}</div>
                            <div class="text-sm text-gray-500">{{ $course->certificates_count }} sertifikat</div>
                        </a>
                    @endforeach
                </div>
                @if($courses->count() > 8)
                    <div class="mt-4 text-center">
                        <span class="text-sm text-gray-500">Dan {{ $courses->count() - 8 }} kursus lainnya...</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Aksi Massal
                </h3>
                <div class="flex flex-wrap gap-4">
                    <!-- Actions for selected certificates -->
                    <div id="selected-actions" style="display: none;" class="flex gap-4 items-center border-r pr-4">
                        <span class="text-sm text-gray-600">
                            <span id="selected-count">0</span> sertifikat dipilih
                        </span>
                        <button onclick="bulkAction('download')"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            📥 Download Terpilih
                        </button>
                        <button onclick="bulkAction('update_template')"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            🔄 Update Template
                        </button>
                        <button onclick="bulkAction('delete')"
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            🗑️ Hapus Terpilih
                        </button>
                    </div>

                    <!-- Actions for all certificates -->
                    <div class="flex gap-4">
                        <button onclick="downloadAllCertificates()"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            📦 Download Semua ({{ $certificates->total() }} sertifikat)
                        </button>
                    </div>
                </div>

                <!-- Download Progress Indicator -->
                <div id="download-progress" style="display: none;" class="mt-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex items-center">
                            <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-blue-800 font-medium" id="progress-text">Memproses download...</span>
                        </div>
                        <div class="mt-2 w-full bg-blue-200 rounded-full h-2">
                            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-blue-600 mt-2">
                            File akan otomatis terdownload setelah selesai. Harap jangan menutup halaman ini.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificates Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Daftar Sertifikat
                        @if(request('course_id') || request('search'))
                            <span class="text-sm font-normal text-gray-500">
                                ({{ $certificates->total() }} hasil)
                            </span>
                        @endif
                    </h3>
                    <div class="flex items-center">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        <label for="select-all" class="ml-2 text-sm text-gray-600">Pilih Semua</label>
                    </div>
                </div>
            </div>
            
            @if($certificates->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($certificates as $certificate)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" name="certificate_ids[]" value="{{ $certificate->id }}" 
                                           class="certificate-checkbox rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $certificate->user->name }}
                                            </div>
                                            <div class="ml-2 flex-shrink-0">
                                                @if($certificate->fileExists())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        ✅ Tersedia
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        ❌ File Hilang
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            📚 {{ $certificate->course->title }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            📅 {{ $certificate->issued_at->format('d M Y H:i') }} • 
                                            🔗 {{ $certificate->certificate_code }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    @if($certificate->fileExists())
                                        <a href="{{ route('certificates.download', $certificate) }}" 
                                           class="bg-green-100 hover:bg-green-200 text-green-800 font-medium py-1 px-3 rounded text-sm transition duration-150 ease-in-out"
                                           title="Lihat Sertifikat">
                                            Download
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('certificates.verify', $certificate->certificate_code) }}" 
                                       target="_blank"
                                       class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-medium py-1 px-3 rounded text-sm transition duration-150 ease-in-out"
                                       title="Verifikasi Publik">
                                        Lihat
                                    </a>
                                    
                                    <button onclick="showUpdateTemplateModal({{ $certificate->id }}, '{{ $certificate->user->name }}', '{{ $certificate->certificateTemplate->name ?? 'Template Tidak Ada' }}')" 
                                            class="bg-orange-100 hover:bg-orange-200 text-orange-800 font-medium py-1 px-3 rounded text-sm transition duration-150 ease-in-out"
                                            title="Update Template">
                                        Update
                                    </button>
                                    
                                    <button onclick="deleteCertificate({{ $certificate->id }})" 
                                            class="bg-red-100 hover:bg-red-200 text-red-800 font-medium py-1 px-3 rounded text-sm transition duration-150 ease-in-out"
                                            title="Hapus Sertifikat">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $certificates->withQueryString()->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <div class="text-6xl mb-4">📜</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada sertifikat ditemukan</h3>
                        <p class="text-sm text-gray-500">
                            @if(request('search') || request('course_id'))
                                Coba ubah filter pencarian Anda.
                            @else
                                Belum ada sertifikat yang dibuat.
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Template Modal -->
<div id="updateTemplateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">Update Template Sertifikat</h3>
                <button onclick="closeUpdateTemplateModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="updateTemplateForm" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Peserta</label>
                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded" id="participant-name"></p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template Saat Ini</label>
                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded" id="current-template"></p>
                </div>
                
                <div class="mb-4">
                    <label for="certificate_template_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Template Baru (Opsional)
                    </label>
                    <select name="certificate_template_id" id="certificate_template_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">-- Gunakan template yang sama --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Kosongkan jika hanya ingin meregenerasi dengan template saat ini
                    </p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeUpdateTemplateModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        Update Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const certificateCheckboxes = document.querySelectorAll('.certificate-checkbox');
    const bulkActionsDiv = document.getElementById('bulk-actions');

    // Handle select all
    selectAllCheckbox.addEventListener('change', function() {
        certificateCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActions();
    });

    // Handle individual checkbox changes
    certificateCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkActions);
    });

    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.certificate-checkbox:checked');
        const selectedCountSpan = document.getElementById('selected-count');
        const selectedActionsDiv = document.getElementById('selected-actions');

        if (checkedBoxes.length > 0) {
            selectedActionsDiv.style.display = 'flex';
            if (selectedCountSpan) {
                selectedCountSpan.textContent = checkedBoxes.length;
            }
        } else {
            selectedActionsDiv.style.display = 'none';
        }
    }
});

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.certificate-checkbox:checked');
    const certificateIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (certificateIds.length === 0) {
        alert('Pilih minimal satu sertifikat');
        return;
    }

    const actionMessages = {
        'delete': 'menghapus',
        'update_template': 'memperbarui template',
        'download': 'mengunduh'
    };
    const actionText = actionMessages[action] || action;

    if (!confirm(`Apakah Anda yakin ingin ${actionText} ${certificateIds.length} sertifikat?`)) {
        return;
    }

    // For download action, use form submission to trigger file download
    if (action === 'download') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("certificate-management.bulk-action") }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);

        certificateIds.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'certificate_ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });

        document.body.appendChild(form);
        form.submit();

        // Clear selection after download
        setTimeout(() => {
            checkedBoxes.forEach(cb => cb.checked = false);
            document.getElementById('select-all').checked = false;
            document.getElementById('bulk-actions').style.display = 'none';
        }, 1000);

        return;
    }

    // For other actions, use AJAX
    fetch('{{ route("certificate-management.bulk-action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            action: action,
            certificate_ids: certificateIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function showUpdateTemplateModal(certificateId, participantName, currentTemplate) {
    document.getElementById('participant-name').textContent = participantName;
    document.getElementById('current-template').textContent = currentTemplate;
    document.getElementById('updateTemplateForm').action = `/certificate-management/${certificateId}/update-template`;
    document.getElementById('certificate_template_id').value = '';
    document.getElementById('updateTemplateModal').classList.remove('hidden');
}

function closeUpdateTemplateModal() {
    document.getElementById('updateTemplateModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('updateTemplateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUpdateTemplateModal();
    }
});

function deleteCertificate(certificateId) {
    if (!confirm('Apakah Anda yakin ingin menghapus sertifikat ini? File PDF juga akan dihapus.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/certificates/${certificateId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Download all certificates with current filters
function downloadAllCertificates() {
    const params = new URLSearchParams(window.location.search);
    const courseId = params.get('course_id') || '';
    const search = params.get('search') || '';

    const totalCount = {{ $certificates->total() }};

    if (totalCount === 0) {
        alert('Tidak ada sertifikat yang bisa didownload');
        return;
    }

    if (!confirm(`Anda akan mendownload ${totalCount} sertifikat. Proses ini mungkin memakan waktu. Lanjutkan?`)) {
        return;
    }

    // Show progress indicator
    document.getElementById('download-progress').style.display = 'block';
    document.getElementById('progress-text').textContent = 'Mempersiapkan download...';
    document.getElementById('progress-bar').style.width = '0%';

    // Start batch download process
    startBatchDownload(courseId, search, totalCount);
}

async function startBatchDownload(courseId, search, totalCount) {
    try {
        // Request server to prepare the download in batches
        const response = await fetch('{{ route("certificate-management.download-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                course_id: courseId,
                search: search
            })
        });

        const data = await response.json();

        if (data.success) {
            // Poll for download status
            pollDownloadStatus(data.batch_id, totalCount);
        } else {
            document.getElementById('download-progress').style.display = 'none';
            alert(data.message || 'Gagal memulai download');
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('download-progress').style.display = 'none';
        alert('Terjadi kesalahan saat memulai download');
    }
}

async function pollDownloadStatus(batchId, totalCount) {
    const pollInterval = setInterval(async () => {
        try {
            const response = await fetch(`{{ url('certificate-management/download-status') }}/${batchId}`);
            const data = await response.json();

            if (data.status === 'processing') {
                const progress = Math.round((data.processed / totalCount) * 100);
                document.getElementById('progress-bar').style.width = progress + '%';
                document.getElementById('progress-text').textContent =
                    `Memproses ${data.processed} dari ${totalCount} sertifikat...`;
            } else if (data.status === 'completed') {
                clearInterval(pollInterval);
                document.getElementById('progress-bar').style.width = '100%';
                document.getElementById('progress-text').textContent = 'Download siap!';

                // Trigger download
                window.location.href = `{{ url('certificate-management/download-zip') }}/${batchId}`;

                // Hide progress after a delay
                setTimeout(() => {
                    document.getElementById('download-progress').style.display = 'none';
                    document.getElementById('progress-bar').style.width = '0%';
                }, 3000);
            } else if (data.status === 'failed') {
                clearInterval(pollInterval);
                document.getElementById('download-progress').style.display = 'none';
                alert('Download gagal: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Polling error:', error);
            clearInterval(pollInterval);
            document.getElementById('download-progress').style.display = 'none';
            alert('Terjadi kesalahan saat memantau progress download');
        }
    }, 2000); // Poll every 2 seconds
}
</script>
</x-app-layout>