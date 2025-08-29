<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 -mx-4 -my-2 px-4 py-8 sm:px-6 lg:px-8 rounded-2xl shadow-lg">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <nav class="flex text-sm text-purple-100 mb-2" aria-label="Breadcrumb">
                        <a href="{{ route('certificate-management.index') }}" class="hover:text-white">Manajemen Sertifikat</a>
                        <span class="mx-2">›</span>
                        <span class="text-white">{{ $course->title }}</span>
                    </nav>
                    <h2 class="text-white text-3xl font-bold leading-tight">
                        {{ $course->title }}
                    </h2>
                    <p class="text-purple-100 mt-2">
                        {{ __('Kelola sertifikat untuk kursus ini') }}
                    </p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('certificate-management.index') }}" 
                       class="bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Course Information -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $certificates->total() }}</div>
                        <div class="text-sm text-gray-500">Total Sertifikat</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $course->enrolledUsers->count() }}</div>
                        <div class="text-sm text-gray-500">Total Peserta</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $certificates->total() > 0 ? number_format(($certificates->total() / $course->enrolledUsers->count()) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-sm text-gray-500">Tingkat Penyelesaian</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('certificate-management.by-course', $course) }}" class="flex flex-wrap gap-4">
                    <!-- Search by Name -->
                    <div class="flex-1 min-w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nama peserta..." 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Cari
                        </button>
                        <a href="{{ route('certificate-management.by-course', $course) }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white shadow rounded-lg mb-6" id="bulk-actions" style="display: none;">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi Massal</h3>
                <div class="flex gap-4">
                    <button onclick="bulkAction('delete')" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        🗑️ Hapus Terpilih
                    </button>
                    <button onclick="bulkAction('update_template')" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        🔄 Update Template
                    </button>
                </div>
            </div>
        </div>

        <!-- Certificates Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Daftar Sertifikat Peserta
                        @if(request('search'))
                            <span class="text-sm font-normal text-gray-500">
                                ({{ $certificates->total() }} hasil pencarian)
                            </span>
                        @else
                            <span class="text-sm font-normal text-gray-500">
                                ({{ $certificates->total() }} total)
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
                                    <div class="ml-4 flex items-center">
                                        <!-- Avatar/Initial -->
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-semibold">
                                                {{ strtoupper(substr($certificate->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        
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
                                                📧 {{ $certificate->user->email }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                📅 Diterbitkan: {{ $certificate->issued_at->format('d M Y H:i') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                🔗 Kode: {{ $certificate->certificate_code }}
                                            </div>
                                            @if($certificate->certificateTemplate)
                                                <div class="text-sm text-gray-500">
                                                    📋 Template: {{ $certificate->certificateTemplate->name }}
                                                </div>
                                            @endif
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
                                    
                                    <button onclick="updateTemplate({{ $certificate->id }})" 
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
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            @if(request('search'))
                                Tidak ada sertifikat yang cocok dengan pencarian
                            @else
                                Belum ada sertifikat untuk kursus ini
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500">
                            @if(request('search'))
                                Coba ubah kata kunci pencarian Anda.
                            @else
                                Sertifikat akan muncul di sini setelah peserta menyelesaikan kursus.
                            @endif
                        </p>
                    </div>
                </div>
            @endif
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
        if (checkedBoxes.length > 0) {
            bulkActionsDiv.style.display = 'block';
        } else {
            bulkActionsDiv.style.display = 'none';
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

    const actionText = action === 'delete' ? 'menghapus' : 'memperbarui template';
    if (!confirm(`Apakah Anda yakin ingin ${actionText} ${certificateIds.length} sertifikat?`)) {
        return;
    }

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

function updateTemplate(certificateId) {
    if (!confirm('Apakah Anda yakin ingin memperbarui template sertifikat ini?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/certificate-management/${certificateId}/update-template`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
}

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
</script>
</x-app-layout>