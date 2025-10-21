@extends('layouts.app')

@section('title', 'Force Complete Contents')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Force Complete Contents</h1>

        <!-- Course Selection Form -->
        <form method="GET" action="{{ route('admin.force-complete.index') }}" class="mb-6">
            <div class="mb-4">
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Kursus</label>
                <select id="course_id" name="course_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">-- Pilih Kursus --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (isset($selectedCourse) && $selectedCourse && $selectedCourse->id == $course->id) ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tampilkan Peserta
            </button>
        </form>

        @if(isset($selectedCourse) && $selectedCourse)
            <div class="border-b border-gray-200 pb-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <h2 class="text-xl font-semibold text-gray-700">Kursus: {{ $selectedCourse->title }}</h2>

                    @if($participants->count() > 0)
                        <form method="POST" action="{{ route('admin.force-complete.complete-all') }}" class="inline-flex items-center gap-3">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                            <label class="inline-flex items-center text-sm">
                                <input type="checkbox" name="generate_certificate" value="1" class="mr-2">
                                Generate sertifikat untuk peserta yang eligible
                            </label>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm" onclick="return confirm('Yakin ingin menandai SELESAI semua konten untuk SEMUA peserta di kursus ini?')">
                                Force Complete Semua Peserta
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($participants->count() > 0)
                <!-- Bulk Action Panel -->
                <div id="bulkActionPanel" class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded hidden">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div class="text-blue-800 font-medium">
                            <span id="selectedCount">0</span> peserta dipilih
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <label class="inline-flex items-center text-sm text-blue-800">
                                <input type="checkbox" id="bulkGenerateCert" class="mr-2">
                                Generate sertifikat otomatis
                            </label>
                            <button onclick="bulkForceComplete()" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Force Complete Terpilih
                            </button>
                            <button onclick="bulkGenerateCertificates()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Generate Sertifikat Terpilih
                            </button>
                            <button onclick="clearSelection()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-sm">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-3 flex justify-between items-center">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="selectAll" class="mr-2">
                        <span class="text-sm font-medium text-gray-700">Pilih Semua</span>
                    </label>
                    <span class="text-sm text-gray-600">Total: {{ $participants->count() }} peserta</span>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @foreach($participants as $p)
                        @php
                            $u = $p['user'];
                            $progress = $p['progress'];
                        @endphp
                        <div class="border rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="flex items-center gap-3 flex-1">
                                <input type="checkbox" class="participant-checkbox" value="{{ $u->id }}" data-name="{{ $u->name }}">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800">{{ $u->name }}</div>
                                    <div class="text-gray-500 text-sm">{{ $u->email }}</div>
                                    <div class="text-sm mt-1">
                                        Progres:
                                        <span class="font-medium {{ $progress['progress_percentage'] >= 100 ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $progress['progress_percentage'] ?? 0 }}%
                                        </span>
                                        ({{ $progress['completed_count'] ?? 0 }}/{{ $progress['total_count'] ?? 0 }})
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.force-complete.complete') }}" class="flex items-center gap-3">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                <label class="inline-flex items-center text-sm">
                                    <input type="checkbox" name="generate_certificate" value="1" class="mr-2">
                                    Generate sertifikat (jika memenuhi syarat)
                                </label>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap" onclick="return confirm('Yakin ingin menandai SELESAI semua konten untuk {{ $u->name }}?')">
                                    Force Complete
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Tidak ada peserta pada kursus ini.</p>
            @endif
        @else
            <div class="text-center py-8 text-gray-500">
                <p>Silakan pilih kursus terlebih dahulu.</p>
            </div>
        @endif
    </div>
    <p class="text-xs text-gray-400 mt-4">Catatan: Force Complete akan menandai konten non-quiz sebagai selesai, membuat attempt lulus untuk quiz (jika belum), dan menyelesaikan essay dengan skor 0/feedback default sesuai mode. Opsi generate sertifikat hanya berlaku jika peserta memenuhi syarat.</p>
    <p class="text-xs text-gray-400 mt-2">💡 Tip: Untuk banyak peserta (>50), proses akan berjalan di background menggunakan queue untuk performa optimal.</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    const bulkActionPanel = document.getElementById('bulkActionPanel');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Handle select all
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            participantCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionPanel();
        });
    }

    // Handle individual checkbox changes
    participantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionPanel);
    });

    function updateBulkActionPanel() {
        const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            bulkActionPanel.classList.remove('hidden');
            selectedCountSpan.textContent = count;
        } else {
            bulkActionPanel.classList.add('hidden');
        }

        // Update select all checkbox state
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = count === participantCheckboxes.length && count > 0;
        }
    }
});

function getSelectedUserIds() {
    const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
    return Array.from(checkedBoxes).map(cb => cb.value);
}

function clearSelection() {
    document.querySelectorAll('.participant-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('bulkActionPanel').classList.add('hidden');
    if (document.getElementById('selectAll')) {
        document.getElementById('selectAll').checked = false;
    }
}

function bulkForceComplete() {
    const userIds = getSelectedUserIds();
    if (userIds.length === 0) {
        alert('Pilih minimal satu peserta');
        return;
    }

    const generateCert = document.getElementById('bulkGenerateCert').checked;
    const courseId = {{ $selectedCourse->id ?? 'null' }};

    if (!courseId) {
        alert('Course ID tidak ditemukan');
        return;
    }

    const message = `Yakin ingin force complete ${userIds.length} peserta?` +
        (generateCert ? '\n\nSertifikat akan digenerate otomatis untuk peserta yang memenuhi syarat.' : '') +
        (userIds.length > 50 ? '\n\nProses akan berjalan di background (queue).' : '');

    if (!confirm(message)) {
        return;
    }

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.force-complete.bulk") }}';

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    const courseInput = document.createElement('input');
    courseInput.type = 'hidden';
    courseInput.name = 'course_id';
    courseInput.value = courseId;
    form.appendChild(courseInput);

    userIds.forEach(userId => {
        const userInput = document.createElement('input');
        userInput.type = 'hidden';
        userInput.name = 'user_ids[]';
        userInput.value = userId;
        form.appendChild(userInput);
    });

    if (generateCert) {
        const certInput = document.createElement('input');
        certInput.type = 'hidden';
        certInput.name = 'generate_certificate';
        certInput.value = '1';
        form.appendChild(certInput);
    }

    document.body.appendChild(form);
    form.submit();
}

function bulkGenerateCertificates() {
    const userIds = getSelectedUserIds();
    if (userIds.length === 0) {
        alert('Pilih minimal satu peserta');
        return;
    }

    const courseId = {{ $selectedCourse->id ?? 'null' }};

    if (!courseId) {
        alert('Course ID tidak ditemukan');
        return;
    }

    const message = `Yakin ingin generate sertifikat untuk ${userIds.length} peserta?` +
        (userIds.length > 50 ? '\n\nProses akan berjalan di background (queue).' : '');

    if (!confirm(message)) {
        return;
    }

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.force-complete.bulk-certificates") }}';

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    const courseInput = document.createElement('input');
    courseInput.type = 'hidden';
    courseInput.name = 'course_id';
    courseInput.value = courseId;
    form.appendChild(courseInput);

    userIds.forEach(userId => {
        const userInput = document.createElement('input');
        userInput.type = 'hidden';
        userInput.name = 'user_ids[]';
        userInput.value = userId;
        form.appendChild(userInput);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection

