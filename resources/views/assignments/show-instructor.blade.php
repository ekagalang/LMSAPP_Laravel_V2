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
                        @if($assignment->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif

                        @if(!$assignment->show_to_students)
                            <span class="badge bg-warning">Tersembunyi</span>
                        @endif

                        @if($assignment->due_date)
                            @if($assignment->due_date->isPast())
                                <span class="badge bg-danger">Terlambat</span>
                            @elseif($assignment->due_date->diffInDays() <= 1)
                                <span class="badge bg-warning">Segera</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div>
                    <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary">
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
                                            <i class="fas fa-file me-1"></i>File saja
                                        @elseif($assignment->submission_type === 'link')
                                            <i class="fas fa-link me-1"></i>Link saja
                                        @else
                                            <i class="fas fa-file me-1"></i><i class="fas fa-link me-1"></i>File atau Link
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Poin Maksimal:</h6>
                                    <p>{{ $assignment->max_points }} poin</p>
                                </div>
                            </div>

                            @if($assignment->submission_type === 'file' || $assignment->submission_type === 'both')
                                <div class="mb-3">
                                    <h6>Pengaturan File:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Maksimal file:</strong> {{ $assignment->max_files ?? 1 }}</li>
                                        @if($assignment->max_file_size)
                                            <li><strong>Ukuran maksimal:</strong> {{ $assignment->getFileSizeFormatted() }}</li>
                                        @endif
                                        @if($assignment->allowed_file_types)
                                            <li>
                                                <strong>Tipe file yang diizinkan:</strong>
                                                <br>
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

                    <!-- Submissions -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Pengumpulan ({{ $submissions->count() }})</h5>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-1"></i>Ekspor
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($submissions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Siswa</th>
                                                <th>Waktu Pengumpulan</th>
                                                <th>Status</th>
                                                <th>Nilai</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($submissions as $submission)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $submission->user->name }}</div>
                                                    <small class="text-muted">{{ $submission->user->email }}</small>
                                                </td>
                                                <td>
                                                    @if($submission->submitted_at)
                                                        <div>{{ $submission->submitted_at->format('d M Y H:i') }}</div>
                                                        @if($assignment->due_date && $submission->submitted_at->gt($assignment->due_date))
                                                            <small class="text-danger">Terlambat</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Draft</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($submission->status === 'graded')
                                                        <span class="badge bg-success">Dinilai</span>
                                                    @elseif($submission->status === 'submitted')
                                                        <span class="badge bg-warning">Menunggu</span>
                                                    @elseif($submission->status === 'returned')
                                                        <span class="badge bg-info">Dikembalikan</span>
                                                    @else
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($submission->points_earned !== null)
                                                        <div class="fw-bold">{{ $submission->points_earned }}/{{ $assignment->max_points }}</div>
                                                        <small class="text-muted">{{ number_format($submission->grade, 1) }}%</small>
                                                    @else
                                                        <span class="text-muted">Belum dinilai</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('assignments.submissions.show', [$assignment, $submission]) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                    <h6 class="text-muted">Belum ada pengumpulan</h6>
                                    <p class="text-muted">Siswa belum mengumpulkan tugas ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Summary Stats -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Statistik</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $totalSubmissions = $submissions->count();
                                $submittedCount = $submissions->where('status', '!=', 'draft')->count();
                                $gradedCount = $submissions->where('status', 'graded')->count();
                                $averageGrade = $submissions->where('status', 'graded')->avg('grade');
                            @endphp

                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="fw-bold h4 text-primary">{{ $totalSubmissions }}</div>
                                    <small class="text-muted">Total Pengumpulan</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="fw-bold h4 text-success">{{ $submittedCount }}</div>
                                    <small class="text-muted">Dikumpulkan</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="fw-bold h4 text-info">{{ $gradedCount }}</div>
                                    <small class="text-muted">Dinilai</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="fw-bold h4 text-warning">
                                        {{ $averageGrade ? number_format($averageGrade, 1) . '%' : '-' }}
                                    </div>
                                    <small class="text-muted">Rata-rata Nilai</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Jadwal</h5>
                        </div>
                        <div class="card-body">
                            @if($assignment->due_date)
                                <div class="mb-3">
                                    <h6>Tenggat Waktu:</h6>
                                    <p class="mb-1">{{ $assignment->due_date->format('d M Y H:i') }}</p>
                                    @if($assignment->due_date->isPast())
                                        <small class="text-danger">Sudah lewat</small>
                                    @else
                                        <small class="text-success">{{ $assignment->due_date->diffForHumans() }}</small>
                                    @endif
                                </div>
                            @endif

                            @if($assignment->allow_late_submission && $assignment->late_submission_until)
                                <div class="mb-3">
                                    <h6>Batas Akhir Terlambat:</h6>
                                    <p class="mb-1">{{ $assignment->late_submission_until->format('d M Y H:i') }}</p>
                                    @if($assignment->late_penalty > 0)
                                        <small class="text-warning">Penalti: {{ $assignment->late_penalty }}%</small>
                                    @endif
                                </div>
                            @endif

                            <div class="mb-3">
                                <h6>Dibuat:</h6>
                                <p class="mb-0">{{ $assignment->created_at->format('d M Y H:i') }}</p>
                                <small class="text-muted">oleh {{ $assignment->creator->name }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aksi Cepat</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit me-2"></i>Edit Tugas
                                </a>

                                @if($assignment->is_active)
                                    <form action="{{ route('assignments.update', $assignment) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="0">
                                        <button type="submit" class="btn btn-outline-warning w-100">
                                            <i class="fas fa-pause me-2"></i>Nonaktifkan
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('assignments.update', $assignment) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="1">
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="fas fa-play me-2"></i>Aktifkan
                                        </button>
                                    </form>
                                @endif

                                <button type="button" class="btn btn-outline-danger"
                                        onclick="confirmDelete({{ $assignment->id }})">
                                    <i class="fas fa-trash me-2"></i>Hapus Tugas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tugas ini? Semua pengumpulan akan ikut terhapus dan tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(assignmentId) {
    const form = document.getElementById('deleteForm');
    form.action = `/assignments/${assignmentId}`;

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
@endsection