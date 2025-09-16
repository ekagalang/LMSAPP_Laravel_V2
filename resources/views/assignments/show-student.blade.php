@extends('layouts.app')

@section('title', $assignment->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ $assignment->title }}</h1>
                    <div class="d-flex align-items-center gap-2">
                        @if($assignment->due_date)
                            @if($assignment->due_date->isPast())
                                <span class="badge bg-danger">Terlambat</span>
                            @elseif($assignment->due_date->diffInDays() <= 1)
                                <span class="badge bg-warning">Segera</span>
                            @else
                                <span class="badge bg-success">Tersedia</span>
                            @endif
                        @endif

                        @if($userSubmission)
                            @if($userSubmission->status === 'graded')
                                <span class="badge bg-success">Dinilai</span>
                            @elseif($userSubmission->status === 'submitted')
                                <span class="badge bg-info">Dikumpulkan</span>
                            @elseif($userSubmission->status === 'returned')
                                <span class="badge bg-warning">Dikembalikan</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div>
                    @if(!$userSubmission || $userSubmission->canEdit())
                        @if($assignment->canSubmit())
                            <a href="{{ route('assignments.submissions.create', $assignment) }}" class="btn btn-primary me-2">
                                <i class="fas fa-upload me-2"></i>
                                {{ $userSubmission ? 'Edit Pengumpulan' : 'Kumpulkan Tugas' }}
                            </a>
                        @endif
                    @endif
                    <a href="{{ route('assignments.student') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <!-- Assignment Details -->
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

                            @if($assignment->submission_type === 'file' || $assignment->submission_type === 'both')
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
                            @endif
                        </div>
                    </div>

                    <!-- Submission Status -->
                    @if($userSubmission)
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Status Pengumpulan</h5>
                                <div>
                                    @if($userSubmission->canEdit())
                                        <a href="{{ route('assignments.submissions.create', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                    @endif
                                    <a href="{{ route('assignments.submissions.show', [$assignment, $userSubmission]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h6>Status:</h6>
                                        @if($userSubmission->status === 'graded')
                                            <span class="badge bg-success">Dinilai</span>
                                        @elseif($userSubmission->status === 'submitted')
                                            <span class="badge bg-info">Dikumpulkan</span>
                                        @elseif($userSubmission->status === 'returned')
                                            <span class="badge bg-warning">Dikembalikan</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <h6>Dikumpulkan:</h6>
                                        <p class="mb-0">
                                            @if($userSubmission->submitted_at)
                                                {{ $userSubmission->submitted_at->format('d M Y H:i') }}
                                                @if($assignment->due_date && $userSubmission->submitted_at->gt($assignment->due_date))
                                                    <br><small class="text-danger">Terlambat</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Belum dikumpulkan</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <h6>Nilai:</h6>
                                        @if($userSubmission->points_earned !== null)
                                            <div class="fw-bold text-success">{{ $userSubmission->points_earned }}/{{ $assignment->max_points }}</div>
                                            <small class="text-muted">{{ number_format($userSubmission->grade, 1) }}%</small>
                                        @else
                                            <span class="text-muted">Belum dinilai</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <h6>Percobaan:</h6>
                                        <p class="mb-0">#{{ $userSubmission->attempt_number }}</p>
                                    </div>
                                </div>

                                @if($userSubmission->submission_text)
                                    <div class="mt-3">
                                        <h6>Teks Pengumpulan:</h6>
                                        <div class="p-3 bg-light rounded">
                                            {{ $userSubmission->submission_text }}
                                        </div>
                                    </div>
                                @endif

                                @if($userSubmission->submission_link)
                                    <div class="mt-3">
                                        <h6>Link:</h6>
                                        <a href="{{ $userSubmission->submission_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>Buka Link
                                        </a>
                                    </div>
                                @endif

                                @if($userSubmission->file_paths)
                                    <div class="mt-3">
                                        <h6>File yang Diunggah:</h6>
                                        <div class="list-group list-group-flush">
                                            @foreach($userSubmission->file_paths as $index => $filePath)
                                                @php
                                                    $metadata = $userSubmission->file_metadata[$index] ?? [];
                                                    $fileName = $metadata['original_name'] ?? basename($filePath);
                                                    $fileSize = isset($metadata['size']) ? round($metadata['size'] / 1024, 1) . ' KB' : '';
                                                @endphp
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file me-2 text-primary"></i>
                                                        <span class="fw-bold">{{ $fileName }}</span>
                                                        @if($fileSize)
                                                            <small class="text-muted">({{ $fileSize }})</small>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('assignments.submissions.download', [$assignment, $userSubmission, $index]) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($userSubmission->instructor_feedback)
                                    <div class="mt-3">
                                        <h6>Feedback Pengajar:</h6>
                                        <div class="alert alert-info">
                                            {{ $userSubmission->instructor_feedback }}
                                        </div>
                                        @if($userSubmission->graded_at)
                                            <small class="text-muted">
                                                Dinilai pada {{ $userSubmission->graded_at->format('d M Y H:i') }}
                                                @if($userSubmission->grader)
                                                    oleh {{ $userSubmission->grader->name }}
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- No Submission Yet -->
                        <div class="card mb-4">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-upload fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Pengumpulan</h5>
                                <p class="text-muted">Anda belum mengumpulkan tugas ini.</p>
                                @if($assignment->canSubmit())
                                    <a href="{{ route('assignments.submissions.create', $assignment) }}" class="btn btn-primary">
                                        <i class="fas fa-upload me-2"></i>Kumpulkan Sekarang
                                    </a>
                                @else
                                    <p class="text-danger">Pengumpulan sudah ditutup.</p>
                                @endif
                            </div>
                        </div>
                    @endif
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

                            <div class="mb-3">
                                <h6>Pembuat:</h6>
                                <p class="mb-0">{{ $assignment->creator->name }}</p>
                                <small class="text-muted">{{ $assignment->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if(!$userSubmission || $userSubmission->canEdit())
                                    @if($assignment->canSubmit())
                                        <a href="{{ route('assignments.submissions.create', $assignment) }}" class="btn btn-primary">
                                            <i class="fas fa-upload me-2"></i>
                                            {{ $userSubmission ? 'Edit Pengumpulan' : 'Kumpulkan Tugas' }}
                                        </a>
                                    @else
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-lock me-2"></i>Pengumpulan Ditutup
                                        </button>
                                    @endif
                                @endif

                                @if($userSubmission)
                                    <a href="{{ route('assignments.submissions.show', [$assignment, $userSubmission]) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-2"></i>Lihat Detail Pengumpulan
                                    </a>
                                @endif

                                <a href="{{ route('assignments.student') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Tugas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($assignment->due_date && !$assignment->due_date->isPast())
@push('scripts')
<script>
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
</script>
@endpush
@endif
@endsection