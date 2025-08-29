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