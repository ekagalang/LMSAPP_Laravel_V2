@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('courses.index') }}" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Kursus</span>
                            Kursus
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('courses.show', $course) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">{{ $course->title }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">Kelola Periode: {{ $period->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Period Info Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $period->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $course->title }}</p>
                <div class="mt-2 flex items-center space-x-4">
                    @if($period->start_date && $period->end_date)
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}
                        </span>
                    @else
                        <span class="text-sm text-gray-500 italic">
                            <i class="fas fa-calendar mr-1"></i>
                            Tanggal belum ditentukan
                        </span>
                    @endif
                    <span>{!! $period->status_badge !!}</span>
                    @if($period->max_participants)
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-users mr-1"></i>
                        {{ $period->getParticipantCount() }} / {{ $period->max_participants }} peserta
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('course-periods.edit', [$course, $period]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Periode
                </a>
                <a href="javascript:void(0)" onclick="window.history.back()"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Instructors Management -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-chalkboard-teacher mr-2 text-blue-600"></i>
                    Instructor ({{ $period->instructors->count() }})
                </h2>
            </div>
            
            <div class="p-6">
                <!-- Add Instructor Form -->
                @if($availableInstructors->count() > 0)
                <form action="{{ route('course-periods.add-instructor', [$course, $period]) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="flex space-x-2">
                        <select name="user_id" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Pilih Instructor</option>
                            @foreach($availableInstructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->name }} ({{ $instructor->email }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Tambah
                        </button>
                    </div>
                </form>
                @endif

                <!-- Current Instructors -->
                <div class="space-y-2">
                    @forelse($period->instructors as $instructor)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ strtoupper(substr($instructor->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $instructor->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $instructor->email }}</p>
                                </div>
                            </div>
                            <form action="{{ route('course-periods.remove-instructor', [$course, $period, $instructor]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-1" onclick="return confirm('Yakin ingin menghapus instructor ini dari periode?')">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada instructor yang ditugaskan</p>
                    @endforelse
                </div>

                @if($availableInstructors->count() == 0 && $period->instructors->count() == 0)
                    <div class="text-center py-6">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-exclamation-circle text-2xl"></i>
                        </div>
                        <p class="text-sm text-gray-500">Tidak ada instructor yang tersedia.</p>
                        <p class="text-xs text-gray-400 mt-1">Pastikan course ini memiliki instructor terlebih dahulu.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Participants Management -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-users mr-2 text-green-600"></i>
                    Peserta ({{ $period->participants->count() }}{{ $period->max_participants ? '/' . $period->max_participants : '' }})
                </h2>
            </div>
            
            <div class="p-6">
                <!-- Add Participants Form with Multiple Select & Search -->
                @if($availableParticipants->count() > 0 && $period->hasAvailableSlots())
                <form action="{{ route('course-periods.add-participant', [$course, $period]) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="space-y-3">
                        <!-- Search Input -->
                        <div>
                            <input type="text" id="participant-search" placeholder="Cari peserta..." 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                        </div>
                        
                        <!-- Multiple Select -->
                        <div class="border rounded-lg max-h-40 overflow-y-auto bg-gray-50">
                            <div id="participant-list" class="p-2 space-y-1">
                                @foreach($availableParticipants as $participant)
                                    <label class="participant-item flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer" 
                                           data-name="{{ strtolower($participant->name) }}" data-email="{{ strtolower($participant->email) }}">
                                        <input type="checkbox" name="user_ids[]" value="{{ $participant->id }}" 
                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500 mr-3">
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-white text-xs font-medium">{{ strtoupper(substr($participant->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $participant->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $participant->email }}</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div id="no-results" class="p-4 text-center text-gray-500 text-sm hidden">
                                Tidak ada peserta yang ditemukan
                            </div>
                        </div>
                        
                        <!-- Selection Summary & Submit -->
                        <div class="flex items-center justify-between">
                            <span id="selection-count" class="text-sm text-gray-600">Belum ada yang dipilih</span>
                            <div class="flex space-x-2">
                                <button type="button" id="select-all" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                    Pilih Semua
                                </button>
                                <button type="button" id="clear-all" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                    Hapus Pilihan
                                </button>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm disabled:bg-gray-400" disabled id="submit-btn">
                                    <i class="fas fa-plus mr-1"></i>
                                    Tambah Peserta
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                @endif

                @if(!$period->hasAvailableSlots() && $period->max_participants)
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Periode sudah penuh ({{ $period->max_participants }} peserta)
                    </p>
                </div>
                @endif

                <!-- Current Participants with Bulk Actions -->
                @if($period->participants->count() > 0)
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-medium text-gray-900">Peserta Terdaftar</h3>
                        <div class="flex space-x-2">
                            <button type="button" id="select-all-participants" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                Pilih Semua
                            </button>
                            <button type="button" id="bulk-remove-btn" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 disabled:bg-gray-100 disabled:text-gray-400" disabled>
                                <i class="fas fa-trash mr-1"></i>
                                Hapus Terpilih
                            </button>
                        </div>
                    </div>
                    
                    <form id="bulk-remove-form" action="{{ route('course-periods.bulk-remove-participants', [$course, $period]) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
                @endif

                <!-- Current Participants List -->
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($period->participants as $participant)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg participant-row">
                            <div class="flex items-center">
                                <input type="checkbox" class="participant-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500 mr-3" 
                                       data-participant-id="{{ $participant->id }}">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white text-sm font-medium">{{ strtoupper(substr($participant->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $participant->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $participant->email }}</p>
                                </div>
                            </div>
                            <form action="{{ route('course-periods.remove-participant', [$course, $period, $participant]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-1" onclick="return confirm('Yakin ingin menghapus peserta ini dari periode?')">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada peserta yang terdaftar</p>
                    @endforelse
                </div>

                @if($availableParticipants->count() == 0 && $period->participants->count() == 0)
                    <div class="text-center py-6">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-exclamation-circle text-2xl"></i>
                        </div>
                        <p class="text-sm text-gray-500">Tidak ada peserta yang tersedia.</p>
                        <p class="text-xs text-gray-400 mt-1">Pastikan course ini memiliki peserta terlebih dahulu.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $period->instructors->count() }}</div>
            <div class="text-sm text-gray-500">Total Instructor</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $period->participants->count() }}</div>
            <div class="text-sm text-gray-500">Total Peserta</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $period->getAvailableSlots() == PHP_INT_MAX ? '∞' : $period->getAvailableSlots() }}</div>
            <div class="text-sm text-gray-500">Slot Tersedia</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $period->getDurationInDays() }}</div>
            <div class="text-sm text-gray-500">Hari</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('participant-search');
    const participantItems = document.querySelectorAll('.participant-item');
    const noResults = document.getElementById('no-results');
    const participantList = document.getElementById('participant-list');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasResults = false;

            participantItems.forEach(function(item) {
                const name = item.dataset.name;
                const email = item.dataset.email;
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    item.style.display = 'flex';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            if (hasResults) {
                participantList.style.display = 'block';
                noResults.style.display = 'none';
            } else {
                participantList.style.display = 'none';
                noResults.style.display = 'block';
            }
        });
    }

    // Multiple selection functionality
    const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
    const selectionCount = document.getElementById('selection-count');
    const submitBtn = document.getElementById('submit-btn');
    const selectAllBtn = document.getElementById('select-all');
    const clearAllBtn = document.getElementById('clear-all');

    function updateSelectionCount() {
        const selectedCount = document.querySelectorAll('input[name="user_ids[]"]:checked').length;
        
        if (selectedCount === 0) {
            selectionCount.textContent = 'Belum ada yang dipilih';
            submitBtn.disabled = true;
        } else {
            selectionCount.textContent = `${selectedCount} peserta dipilih`;
            submitBtn.disabled = false;
        }
    }

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateSelectionCount);
    });

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const visibleCheckboxes = Array.from(checkboxes).filter(cb => 
                cb.closest('.participant-item').style.display !== 'none'
            );
            
            visibleCheckboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
            updateSelectionCount();
        });
    }

    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });
            updateSelectionCount();
        });
    }

    // Bulk remove functionality
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    const bulkRemoveBtn = document.getElementById('bulk-remove-btn');
    const selectAllParticipantsBtn = document.getElementById('select-all-participants');
    const bulkRemoveForm = document.getElementById('bulk-remove-form');

    function updateBulkRemoveBtn() {
        const selectedParticipants = document.querySelectorAll('.participant-checkbox:checked').length;
        bulkRemoveBtn.disabled = selectedParticipants === 0;
    }

    participantCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateBulkRemoveBtn);
    });

    if (selectAllParticipantsBtn) {
        selectAllParticipantsBtn.addEventListener('click', function() {
            participantCheckboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
            updateBulkRemoveBtn();
        });
    }

    if (bulkRemoveBtn) {
        bulkRemoveBtn.addEventListener('click', function() {
            const selectedParticipants = document.querySelectorAll('.participant-checkbox:checked');
            
            if (selectedParticipants.length === 0) {
                return;
            }

            if (confirm(`Yakin ingin menghapus ${selectedParticipants.length} peserta dari periode ini?`)) {
                // Add selected participant IDs to the form
                selectedParticipants.forEach(function(checkbox) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'participant_ids[]';
                    input.value = checkbox.dataset.participantId;
                    bulkRemoveForm.appendChild(input);
                });
                
                // Submit the form
                bulkRemoveForm.submit();
            }
        });
    }
});
</script>
@endsection