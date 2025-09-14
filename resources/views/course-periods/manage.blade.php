@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="{
        showRegenerateModal: false,
        isGenerating: false,
        isRegenerating: false,

        generateToken() {
            this.isGenerating = true;
            window.generateTokenHandler(this);
        },

        confirmRegenerateToken() {
            this.isRegenerating = true;
            window.confirmRegenerateTokenHandler(this);
        },

        copyToken() {
            window.copyTokenHandler(this);
        }
    }">
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
        <!-- Top Section: Title and Action Buttons -->
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-6">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $period->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $course->title }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-4">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}
                    </span>
                    <span>{!! $period->status_badge !!}</span>
                    @if($period->max_participants)
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-users mr-1"></i>
                        {{ $period->getParticipantCount() }} / {{ $period->max_participants }} peserta
                    </span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:flex-shrink-0">
                <a href="{{ route('course-periods.edit', [$course, $period]) }}"
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Periode
                </a>
                <a href="{{ route('courses.show', $course) }}"
                   class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Kursus
                </a>
            </div>
        </div>

        <!-- Token Section -->
        <div class="border-t border-gray-200 pt-6" id="token-section">
            @if($period->join_token)
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg" id="token-display">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-green-900">Token Bergabung:</p>
                        <p class="text-xl font-mono font-bold text-green-600 mt-1" id="current-token">{{ $period->join_token }}</p>
                        <p class="text-xs text-green-700 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Bagikan token ini kepada peserta untuk bergabung ke periode
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-2 ml-auto">
                        <button type="button"
                                @click="copyToken()"
                                class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium"
                                id="copy-btn">
                            <i class="fas fa-copy mr-2"></i>Salin Token
                        </button>
                        <button type="button"
                                @click="showRegenerateModal = true"
                                :disabled="isRegenerating"
                                class="px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                id="regenerate-btn">
                            <template x-if="!isRegenerating">
                                <span><i class="fas fa-sync mr-2"></i>Generate Baru</span>
                            </template>
                            <template x-if="isRegenerating">
                                <span><i class="fas fa-spinner fa-spin mr-2"></i>Generating...</span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
            @else
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg" id="no-token-display">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700">Token Bergabung:</p>
                        <p class="text-sm text-gray-500 mt-1">Belum ada token untuk periode ini</p>
                        <p class="text-xs text-gray-600 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Token diperlukan agar peserta dapat bergabung ke periode ini
                        </p>
                    </div>
                    <div class="flex justify-end ml-auto">
                        <button type="button"
                                @click="generateToken()"
                                :disabled="isGenerating"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                id="generate-btn">
                            <template x-if="!isGenerating">
                                <span><i class="fas fa-plus mr-2"></i>Buat Token</span>
                            </template>
                            <template x-if="isGenerating">
                                <span><i class="fas fa-spinner fa-spin mr-2"></i>Membuat...</span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
            @endif
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
    <!-- Regenerate Token Confirmation Modal -->
    <div x-show="showRegenerateModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showRegenerateModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showRegenerateModal = false"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

            <div x-show="showRegenerateModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Generate Token Baru
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Yakin ingin membuat token baru? Token yang lama akan tidak bisa digunakan lagi dan peserta yang menggunakan token lama tidak akan bisa bergabung.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            @click="confirmRegenerateToken()"
                            :disabled="isRegenerating"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="!isRegenerating">
                            <span>Ya, Generate Baru</span>
                        </template>
                        <template x-if="isRegenerating">
                            <span><i class="fas fa-spinner fa-spin mr-2"></i>Generating...</span>
                        </template>
                    </button>
                    <button type="button"
                            @click="showRegenerateModal = false"
                            :disabled="isRegenerating"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global token management handlers
window.generateTokenHandler = function(alpineComponent) {
    fetch('{{ route('course-periods.generate-token', [$course, $period]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Create new token display elements
            const tokenSection = document.getElementById('token-section');

            // Create the new token display div
            const tokenDisplay = document.createElement('div');
            tokenDisplay.className = 'p-4 bg-green-50 border border-green-200 rounded-lg';
            tokenDisplay.id = 'token-display';

            tokenDisplay.innerHTML = `
                <div class='flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4'>
                    <div>
                        <p class='text-sm font-medium text-green-900'>Token Bergabung:</p>
                        <p class='text-xl font-mono font-bold text-green-600 mt-1' id='current-token'>${data.token}</p>
                        <p class='text-xs text-green-700 mt-2'>
                            <i class='fas fa-info-circle mr-1'></i>
                            Bagikan token ini kepada peserta untuk bergabung ke periode
                        </p>
                    </div>
                    <div class='flex flex-wrap items-center justify-end gap-2 ml-auto'>
                        <button type='button'
                                onclick='copyTokenDirect("${data.token}")'
                                class='px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium'
                                id='copy-btn'>
                            <i class='fas fa-copy mr-2'></i>Salin Token
                        </button>
                        <button type='button'
                                onclick='showRegenerateModalDirect()'
                                class='px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors text-sm font-medium'
                                id='regenerate-btn'>
                            <i class='fas fa-sync mr-2'></i>Generate Baru
                        </button>
                    </div>
                </div>
            `;

            tokenSection.innerHTML = '';
            tokenSection.appendChild(tokenDisplay);
            showToastGlobal('success', data.message || 'Token berhasil dibuat');
        } else {
            showToastGlobal('error', data.message || 'Gagal membuat token');
        }
        alpineComponent.isGenerating = false;
    })
    .catch(error => {
        console.error('Error:', error);
        showToastGlobal('error', 'Terjadi kesalahan saat membuat token');
        alpineComponent.isGenerating = false;
    });
};

window.confirmRegenerateTokenHandler = function(alpineComponent) {
    fetch('{{ route('course-periods.regenerate-token', [$course, $period]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update token display
            const tokenElement = document.getElementById('current-token');
            if (tokenElement) {
                tokenElement.textContent = data.token;
                // Update onclick handler for copy button
                const copyButton = document.getElementById('copy-btn');
                if (copyButton) {
                    copyButton.setAttribute('onclick', `copyTokenDirect("${data.token}")`);
                }
            }
            showToastGlobal('success', data.message || 'Token berhasil diperbaharui');
        } else {
            showToastGlobal('error', data.message || 'Gagal membuat token baru');
        }
        alpineComponent.isRegenerating = false;
        alpineComponent.showRegenerateModal = false;
    })
    .catch(error => {
        console.error('Error:', error);
        showToastGlobal('error', 'Terjadi kesalahan saat membuat token baru');
        alpineComponent.isRegenerating = false;
        alpineComponent.showRegenerateModal = false;
    });
};

window.copyTokenHandler = function(alpineComponent) {
    const tokenElement = document.getElementById('current-token');
    const token = tokenElement ? tokenElement.textContent : '';

    if (navigator.clipboard && token) {
        navigator.clipboard.writeText(token).then(() => {
            // Show success feedback
            const button = document.getElementById('copy-btn');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Tersalin!';
            button.classList.remove('bg-green-100', 'text-green-700');
            button.classList.add('bg-green-600', 'text-white');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600', 'text-white');
                button.classList.add('bg-green-100', 'text-green-700');
            }, 2000);

            showToastGlobal('success', 'Token berhasil disalin!');
        }).catch(() => {
            // Fallback for older browsers
            copyTokenFallbackGlobal(token);
        });
    } else {
        copyTokenFallbackGlobal(token);
    }
};

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

// Global functions for dynamically created buttons
function copyTokenDirect(token) {
    if (navigator.clipboard && token) {
        navigator.clipboard.writeText(token).then(() => {
            // Show success feedback
            const button = document.getElementById('copy-btn');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Tersalin!';
            button.classList.remove('bg-green-100', 'text-green-700');
            button.classList.add('bg-green-600', 'text-white');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600', 'text-white');
                button.classList.add('bg-green-100', 'text-green-700');
            }, 2000);

            showToastGlobal('success', 'Token berhasil disalin!');
        }).catch(() => {
            copyTokenFallbackGlobal(token);
        });
    } else {
        copyTokenFallbackGlobal(token);
    }
}

function copyTokenFallbackGlobal(token) {
    const textArea = document.createElement('textarea');
    textArea.value = token;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    showToastGlobal('success', 'Token berhasil disalin: ' + token);
}

function showRegenerateModalDirect() {
    // Get Alpine component and set showRegenerateModal to true
    const component = document.querySelector('[x-data]');
    if (component && component._x_dataStack && component._x_dataStack[0]) {
        component._x_dataStack[0].showRegenerateModal = true;
    }
}

function showToastGlobal(type, message) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.innerHTML = `
        <div class='flex items-center'>
            <i class='fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2'></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Remove after delay
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}
</script>

@push('scripts')
<!-- Alpine.js CDN -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endsection