@extends('layouts.app')

@section('title', 'Kumpulkan Tugas: ' . $assignment->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">
                        {{ $existingSubmission ? 'Edit Pengumpulan' : 'Kumpulkan Tugas' }}
                    </h1>
                    <p class="text-muted mb-0">{{ $assignment->title }}</p>
                </div>
                <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <h6>Terjadi kesalahan:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <!-- Assignment Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Detail Tugas</h5>
                        </div>
                        <div class="card-body">
                            @if($assignment->description)
                                <div class="mb-3">
                                    <h6>Deskripsi:</h6>
                                    <p class="text-muted">{{ $assignment->description }}</p>
                                </div>
                            @endif

                            @if($assignment->instructions)
                                <div class="mb-3">
                                    <h6>Instruksi:</h6>
                                    <div class="p-3 bg-light rounded">
                                        {{ $assignment->instructions }}
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Tipe Pengumpulan:</h6>
                                    <p>
                                        @if($assignment->submission_type === 'file')
                                            <i class="fas fa-file me-1 text-primary"></i>Unggah file
                                        @elseif($assignment->submission_type === 'link')
                                            <i class="fas fa-link me-1 text-primary"></i>Kirim link
                                        @else
                                            <i class="fas fa-file me-1 text-primary"></i><i class="fas fa-link me-1 text-primary"></i>File atau Link
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Poin:</h6>
                                    <p class="fw-bold text-success">{{ $assignment->max_points }} poin</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                {{ $existingSubmission ? 'Edit Pengumpulan' : 'Form Pengumpulan' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('assignments.submissions.store', $assignment) }}" method="POST" enctype="multipart/form-data" id="submission-form">
                                @csrf

                                <!-- Text Submission -->
                                <div class="mb-4">
                                    <label for="submission_text" class="form-label">Teks Pengumpulan</label>
                                    <textarea class="form-control @error('submission_text') is-invalid @enderror"
                                              id="submission_text" name="submission_text" rows="5"
                                              placeholder="Tuliskan penjelasan atau keterangan mengenai pengumpulan Anda (opsional)">{{ old('submission_text', $existingSubmission->submission_text ?? '') }}</textarea>
                                    @error('submission_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Link Submission -->
                                @if($assignment->submission_type === 'link' || $assignment->submission_type === 'both')
                                    <div class="mb-4">
                                        <label for="submission_link" class="form-label">
                                            Link Pengumpulan
                                            @if($assignment->submission_type === 'link')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="url" class="form-control @error('submission_link') is-invalid @enderror"
                                               id="submission_link" name="submission_link"
                                               value="{{ old('submission_link', $existingSubmission->submission_link ?? '') }}"
                                               placeholder="https://example.com/your-submission"
                                               {{ $assignment->submission_type === 'link' ? 'required' : '' }}>
                                        @error('submission_link')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Masukkan link ke Google Drive, Dropbox, GitHub, atau platform lainnya.</div>
                                    </div>
                                @endif

                                <!-- File Upload -->
                                @if($assignment->submission_type === 'file' || $assignment->submission_type === 'both')
                                    <div class="mb-4">
                                        <label for="files" class="form-label">
                                            Unggah File
                                            @if($assignment->submission_type === 'file')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        <!-- File Upload Area -->
                                        <div class="file-upload-area border border-2 border-dashed rounded p-4 text-center mb-3"
                                             ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
                                            <div class="upload-content">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Drag & drop file di sini</h5>
                                                <p class="text-muted mb-3">atau</p>
                                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('files').click()">
                                                    <i class="fas fa-file-upload me-2"></i>Pilih File
                                                </button>
                                            </div>
                                        </div>

                                        <input type="file" class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror"
                                               id="files" name="files[]" style="display: none;"
                                               {{ $assignment->max_files > 1 ? 'multiple' : '' }}
                                               {{ $assignment->submission_type === 'file' ? 'required' : '' }}
                                               accept="{{ $assignment->allowed_file_types ? '.' . implode(',.', $assignment->allowed_file_types) : '' }}"
                                               onchange="handleFiles(this.files)">

                                        @error('files')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        @error('files.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <!-- File Info -->
                                        <div class="alert alert-info">
                                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Ketentuan File:</h6>
                                            <ul class="mb-0">
                                                <li><strong>Maksimal file:</strong> {{ $assignment->max_files ?? 1 }}</li>
                                                @if($assignment->max_file_size)
                                                    <li><strong>Ukuran maksimal per file:</strong> {{ $assignment->getFileSizeFormatted() }}</li>
                                                @endif
                                                @if($assignment->allowed_file_types)
                                                    <li>
                                                        <strong>Tipe file yang diizinkan:</strong>
                                                        @foreach($assignment->allowed_file_types as $type)
                                                            <span class="badge bg-secondary me-1">{{ strtoupper($type) }}</span>
                                                        @endforeach
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <!-- Selected Files -->
                                        <div id="selected-files" class="mt-3" style="display: none;">
                                            <h6>File yang Dipilih:</h6>
                                            <div id="file-list" class="list-group"></div>
                                        </div>

                                        <!-- Existing Files -->
                                        @if($existingSubmission && $existingSubmission->file_paths)
                                            <div class="mt-3">
                                                <h6>File yang Sudah Diunggah:</h6>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Jika Anda mengunggah file baru, file lama akan tergantikan.
                                                </div>
                                                <div class="list-group">
                                                    @foreach($existingSubmission->file_paths as $index => $filePath)
                                                        @php
                                                            $metadata = $existingSubmission->file_metadata[$index] ?? [];
                                                            $fileName = $metadata['original_name'] ?? basename($filePath);
                                                            $fileSize = isset($metadata['size']) ? round($metadata['size'] / 1024, 1) . ' KB' : '';
                                                        @endphp
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="fas fa-file me-2 text-success"></i>
                                                                <span class="fw-bold">{{ $fileName }}</span>
                                                                @if($fileSize)
                                                                    <small class="text-muted">({{ $fileSize }})</small>
                                                                @endif
                                                            </div>
                                                            <a href="{{ route('assignments.submissions.download', [$assignment, $existingSubmission, $index]) }}"
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <hr>

                                <!-- Submit Options -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" name="action" value="save_draft" class="btn btn-outline-secondary w-100">
                                            <i class="fas fa-save me-2"></i>Simpan sebagai Draft
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" name="submit_now" value="1" class="btn btn-primary w-100" id="submit-btn">
                                            <i class="fas fa-paper-plane me-2"></i>Kumpulkan Sekarang
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Schedule Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Jadwal</h5>
                        </div>
                        <div class="card-body">
                            @if($assignment->due_date)
                                <div class="mb-3">
                                    <h6>Tenggat Waktu:</h6>
                                    <p class="mb-1 fw-bold">{{ $assignment->due_date->format('d M Y H:i') }}</p>
                                    @if($assignment->due_date->isPast())
                                        <small class="text-danger">Sudah lewat</small>
                                    @else
                                        <small class="text-success">{{ $assignment->due_date->diffForHumans() }}</small>
                                    @endif
                                </div>

                                <!-- Countdown -->
                                @if(!$assignment->due_date->isPast())
                                    <div class="alert alert-warning">
                                        <div id="countdown" class="text-center">
                                            <div class="fw-bold">Sisa Waktu:</div>
                                            <div id="countdown-timer" class="h5 mb-0"></div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Tidak ada tenggat waktu
                                </div>
                            @endif

                            @if($assignment->allow_late_submission && $assignment->late_submission_until)
                                <div class="mb-3">
                                    <h6>Batas Akhir Terlambat:</h6>
                                    <p class="mb-1">{{ $assignment->late_submission_until->format('d M Y H:i') }}</p>
                                    @if($assignment->late_penalty > 0)
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Penalti: {{ $assignment->late_penalty }}%
                                        </small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($existingSubmission)
                        <!-- Current Status -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Status Saat Ini</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6>Status:</h6>
                                    @if($existingSubmission->status === 'graded')
                                        <span class="badge bg-success">Dinilai</span>
                                    @elseif($existingSubmission->status === 'submitted')
                                        <span class="badge bg-info">Dikumpulkan</span>
                                    @elseif($existingSubmission->status === 'returned')
                                        <span class="badge bg-warning">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </div>

                                @if($existingSubmission->submitted_at)
                                    <div class="mb-3">
                                        <h6>Dikumpulkan:</h6>
                                        <p class="mb-0">{{ $existingSubmission->submitted_at->format('d M Y H:i') }}</p>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <h6>Percobaan:</h6>
                                    <p class="mb-0">#{{ $existingSubmission->attempt_number }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Help -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Tips</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                    Simpan sebagai draft untuk mengedit nanti
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Periksa kembali sebelum mengirim
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-info me-2"></i>
                                    Kirim sebelum tenggat waktu
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                    Pastikan file sesuai ketentuan
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// File upload handling
function dragOverHandler(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('border-primary', 'bg-light');
}

function dragLeaveHandler(ev) {
    ev.currentTarget.classList.remove('border-primary', 'bg-light');
}

function dropHandler(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('border-primary', 'bg-light');

    const files = ev.dataTransfer.files;
    document.getElementById('files').files = files;
    handleFiles(files);
}

function handleFiles(files) {
    const fileList = document.getElementById('file-list');
    const selectedFiles = document.getElementById('selected-files');

    if (files.length === 0) {
        selectedFiles.style.display = 'none';
        return;
    }

    selectedFiles.style.display = 'block';
    fileList.innerHTML = '';

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);

        const fileItem = document.createElement('div');
        fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        fileItem.innerHTML = `
            <div>
                <i class="fas fa-file me-2 text-primary"></i>
                <span class="fw-bold">${file.name}</span>
                <small class="text-muted">(${fileSize} MB)</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${i})">
                <i class="fas fa-times"></i>
            </button>
        `;
        fileList.appendChild(fileItem);
    }
}

function removeFile(index) {
    const fileInput = document.getElementById('files');
    const dt = new DataTransfer();

    for (let i = 0; i < fileInput.files.length; i++) {
        if (i !== index) {
            dt.items.add(fileInput.files[i]);
        }
    }

    fileInput.files = dt.files;
    handleFiles(fileInput.files);
}

// Countdown timer
@if($assignment->due_date && !$assignment->due_date->isPast())
document.addEventListener('DOMContentLoaded', function() {
    const dueDate = new Date('{{ $assignment->due_date->toISOString() }}').getTime();
    const countdownElement = document.getElementById('countdown-timer');

    if (countdownElement) {
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = dueDate - now;

            if (distance < 0) {
                clearInterval(timer);
                countdownElement.innerHTML = "<span class='text-danger'>Waktu Habis</span>";
                document.getElementById('submit-btn').disabled = true;
                document.getElementById('submit-btn').innerHTML = '<i class="fas fa-lock me-2"></i>Waktu Habis';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = `${days}h ${hours}j ${minutes}m ${seconds}d`;
        }, 1000);
    }
});
@endif

// Form submission confirmation
document.getElementById('submission-form').addEventListener('submit', function(e) {
    const submitBtn = e.submitter;
    if (submitBtn && submitBtn.name === 'submit_now') {
        if (!confirm('Apakah Anda yakin ingin mengumpulkan tugas ini? Setelah dikumpulkan, Anda tidak dapat mengubahnya lagi.')) {
            e.preventDefault();
        }
    }
});
</script>
@endpush
@endsection