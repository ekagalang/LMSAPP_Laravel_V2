@extends('layouts.app')

@section('title', 'Detail Pengumpulan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Detail Pengumpulan</h1>
                    <p class="text-muted mb-0">{{ $assignment->title }}</p>
                </div>
                <div>
                    @if($isInstructor)
                        <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Tugas
                        </a>
                    @else
                        @if($submission->canEdit())
                            <a href="{{ route('assignments.submissions.create', $assignment) }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a>
                        @endif
                        <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <!-- Submission Details -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Detail Pengumpulan</h5>
                            <div>
                                @if($submission->status === 'graded')
                                    <span class="badge bg-success">Dinilai</span>
                                @elseif($submission->status === 'submitted')
                                    <span class="badge bg-info">Dikumpulkan</span>
                                @elseif($submission->status === 'returned')
                                    <span class="badge bg-warning">Dikembalikan</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <h6>Siswa:</h6>
                                    <p class="mb-0">{{ $submission->user->name }}</p>
                                    <small class="text-muted">{{ $submission->user->email }}</small>
                                </div>
                                <div class="col-md-3">
                                    <h6>Waktu Pengumpulan:</h6>
                                    @if($submission->submitted_at)
                                        <p class="mb-0">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                                        @if($assignment->due_date && $submission->submitted_at->gt($assignment->due_date))
                                            <small class="text-danger">Terlambat</small>
                                        @endif
                                    @else
                                        <p class="mb-0 text-muted">Belum dikumpulkan</p>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <h6>Percobaan:</h6>
                                    <p class="mb-0">#{{ $submission->attempt_number }}</p>
                                </div>
                                <div class="col-md-3">
                                    <h6>Terakhir Diupdate:</h6>
                                    <p class="mb-0">{{ $submission->updated_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>

                            @if($submission->submission_text)
                                <div class="mb-3">
                                    <h6>Teks Pengumpulan:</h6>
                                    <div class="p-3 bg-light rounded">
                                        {{ $submission->submission_text }}
                                    </div>
                                </div>
                            @endif

                            @if($submission->submission_link)
                                <div class="mb-3">
                                    <h6>Link:</h6>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ $submission->submission_link }}" target="_blank" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-external-link-alt me-1"></i>Buka Link
                                        </a>
                                        <small class="text-muted">{{ $submission->submission_link }}</small>
                                    </div>
                                </div>
                            @endif

                            @if($submission->file_paths)
                                <div class="mb-3">
                                    <h6>File yang Diunggah:</h6>
                                    <div class="list-group">
                                        @foreach($submission->file_paths as $index => $filePath)
                                            @php
                                                $metadata = $submission->file_metadata[$index] ?? [];
                                                $fileName = $metadata['original_name'] ?? basename($filePath);
                                                $fileSize = isset($metadata['size']) ? round($metadata['size'] / 1024, 1) . ' KB' : '';
                                                $mimeType = $metadata['mime_type'] ?? '';
                                            @endphp
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-file me-2 text-primary"></i>
                                                    <span class="fw-bold">{{ $fileName }}</span>
                                                    @if($fileSize)
                                                        <small class="text-muted">({{ $fileSize }})</small>
                                                    @endif
                                                    @if($mimeType)
                                                        <span class="badge bg-secondary ms-1">{{ strtoupper(explode('/', $mimeType)[1] ?? '') }}</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('assignments.submissions.download', [$assignment, $submission, $index]) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Grading Section (Instructor Only) -->
                    @if($isInstructor && $submission->status !== 'draft')
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Penilaian</h5>
                            </div>
                            <div class="card-body">
                                @if($submission->status === 'graded')
                                    <!-- Show Existing Grade -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <h6>Nilai:</h6>
                                            <div class="display-6 text-success">{{ $submission->points_earned }}/{{ $assignment->max_points }}</div>
                                            <small class="text-muted">{{ number_format($submission->grade, 1) }}%</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h6>Dinilai oleh:</h6>
                                            <p class="mb-0">{{ $submission->grader->name ?? 'Unknown' }}</p>
                                            <small class="text-muted">{{ $submission->graded_at->format('d M Y H:i') }}</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h6>Status:</h6>
                                            @if($submission->status === 'graded')
                                                <span class="badge bg-success">Dinilai</span>
                                            @else
                                                <span class="badge bg-info">Dikembalikan</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($submission->instructor_feedback)
                                        <div class="mb-3">
                                            <h6>Feedback:</h6>
                                            <div class="p-3 bg-light rounded">
                                                {{ $submission->instructor_feedback }}
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Edit Grade Form -->
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editGradeForm">
                                        <i class="fas fa-edit me-2"></i>Edit Nilai
                                    </button>

                                    <div class="collapse mt-3" id="editGradeForm">
                                        <form action="{{ route('assignments.submissions.grade', [$assignment, $submission]) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="edit_points_earned" class="form-label">Poin yang Diperoleh</label>
                                                        <input type="number" class="form-control" id="edit_points_earned" name="points_earned"
                                                               value="{{ $submission->points_earned }}" min="0" max="{{ $assignment->max_points }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="edit_status" class="form-label">Status</label>
                                                        <select class="form-select" id="edit_status" name="status" required>
                                                            <option value="graded" {{ $submission->status === 'graded' ? 'selected' : '' }}>Dinilai</option>
                                                            <option value="returned" {{ $submission->status === 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_instructor_feedback" class="form-label">Feedback</label>
                                                <textarea class="form-control" id="edit_instructor_feedback" name="instructor_feedback" rows="3">{{ $submission->instructor_feedback }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Update Nilai
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <!-- Grade Form -->
                                    <form action="{{ route('assignments.submissions.grade', [$assignment, $submission]) }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="points_earned" class="form-label">Poin yang Diperoleh <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('points_earned') is-invalid @enderror"
                                                           id="points_earned" name="points_earned" value="{{ old('points_earned') }}"
                                                           min="0" max="{{ $assignment->max_points }}" required>
                                                    <div class="form-text">Maksimal: {{ $assignment->max_points }} poin</div>
                                                    @error('points_earned')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                        <option value="">Pilih status</option>
                                                        <option value="graded" {{ old('status') === 'graded' ? 'selected' : '' }}>Dinilai</option>
                                                        <option value="returned" {{ old('status') === 'returned' ? 'selected' : '' }}>Dikembalikan untuk Revisi</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Persentase</label>
                                                    <div class="form-control-plaintext" id="percentage-display">-</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="instructor_feedback" class="form-label">Feedback untuk Siswa</label>
                                            <textarea class="form-control @error('instructor_feedback') is-invalid @enderror"
                                                      id="instructor_feedback" name="instructor_feedback" rows="3"
                                                      placeholder="Berikan feedback yang konstruktif...">{{ old('instructor_feedback') }}</textarea>
                                            @error('instructor_feedback')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Simpan Nilai
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @elseif(!$isInstructor && $submission->instructor_feedback)
                        <!-- Student View of Feedback -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Feedback Pengajar</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Feedback:</h6>
                                    <p class="mb-0">{{ $submission->instructor_feedback }}</p>
                                </div>
                                @if($submission->graded_at)
                                    <small class="text-muted">
                                        Dinilai pada {{ $submission->graded_at->format('d M Y H:i') }}
                                        @if($submission->grader)
                                            oleh {{ $submission->grader->name }}
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <!-- Grade Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ringkasan Nilai</h5>
                        </div>
                        <div class="card-body text-center">
                            @if($submission->points_earned !== null)
                                <div class="display-4 text-success mb-2">{{ $submission->points_earned }}</div>
                                <div class="h5 text-muted mb-2">dari {{ $assignment->max_points }} poin</div>
                                <div class="h6 text-muted">{{ number_format($submission->grade, 1) }}%</div>

                                @php
                                    $percentage = $submission->grade;
                                    if ($percentage >= 80) {
                                        $gradeClass = 'success';
                                        $gradeLetter = 'A';
                                    } elseif ($percentage >= 70) {
                                        $gradeClass = 'info';
                                        $gradeLetter = 'B';
                                    } elseif ($percentage >= 60) {
                                        $gradeClass = 'warning';
                                        $gradeLetter = 'C';
                                    } else {
                                        $gradeClass = 'danger';
                                        $gradeLetter = 'D';
                                    }
                                @endphp

                                <span class="badge bg-{{ $gradeClass }} fs-6">Grade {{ $gradeLetter }}</span>
                            @else
                                <div class="text-muted">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h6>Belum Dinilai</h6>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Assignment Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Info Tugas</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6>Judul:</h6>
                                <p class="mb-0">{{ $assignment->title }}</p>
                            </div>
                            <div class="mb-3">
                                <h6>Poin Maksimal:</h6>
                                <p class="mb-0">{{ $assignment->max_points }} poin</p>
                            </div>
                            @if($assignment->due_date)
                                <div class="mb-3">
                                    <h6>Tenggat Waktu:</h6>
                                    <p class="mb-0">{{ $assignment->due_date->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                            <div class="mb-3">
                                <h6>Pembuat:</h6>
                                <p class="mb-0">{{ $assignment->creator->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if(!$isInstructor && $submission->canEdit())
                                    <a href="{{ route('assignments.submissions.create', $assignment) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit me-2"></i>Edit Pengumpulan
                                    </a>
                                @endif

                                @if($submission->file_paths)
                                    <button type="button" class="btn btn-outline-success" id="download-all-btn">
                                        <i class="fas fa-download me-2"></i>Download Semua File
                                    </button>
                                @endif

                                @if($isInstructor)
                                    <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Tugas
                                    </a>
                                @else
                                    <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Detail Tugas
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
@if($isInstructor)
// Calculate percentage when points change
document.getElementById('points_earned')?.addEventListener('input', function() {
    const points = parseFloat(this.value) || 0;
    const maxPoints = {{ $assignment->max_points }};
    const percentage = (points / maxPoints * 100).toFixed(1);
    document.getElementById('percentage-display').textContent = percentage + '%';
});
@endif

// Download all files
document.getElementById('download-all-btn')?.addEventListener('click', function() {
    @if($submission->file_paths)
        @foreach($submission->file_paths as $index => $filePath)
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = '{{ route('assignments.submissions.download', [$assignment, $submission, $index]) }}';
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, {{ $index * 500 }});
        @endforeach
    @endif
});
</script>
@endpush
@endsection