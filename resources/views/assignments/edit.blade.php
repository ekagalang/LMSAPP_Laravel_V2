@extends('layouts.app')

@section('title', 'Edit Tugas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Tugas: {{ $assignment->title }}</h1>
                <div>
                    <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye me-2"></i>Lihat
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

            <form action="{{ route('assignments.update', $assignment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informasi Dasar</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title', $assignment->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3">{{ old('description', $assignment->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="instructions" class="form-label">Instruksi Pengerjaan</label>
                                    <textarea class="form-control @error('instructions') is-invalid @enderror"
                                              id="instructions" name="instructions" rows="5">{{ old('instructions', $assignment->instructions) }}</textarea>
                                    @error('instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Berikan instruksi detail tentang bagaimana mengerjakan tugas ini.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Submission Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Pengaturan Pengumpulan</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="submission_type" class="form-label">Tipe Pengumpulan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('submission_type') is-invalid @enderror"
                                            id="submission_type" name="submission_type" required>
                                        <option value="">Pilih tipe pengumpulan</option>
                                        <option value="file" {{ old('submission_type', $assignment->submission_type) === 'file' ? 'selected' : '' }}>File saja</option>
                                        <option value="link" {{ old('submission_type', $assignment->submission_type) === 'link' ? 'selected' : '' }}>Link saja</option>
                                        <option value="both" {{ old('submission_type', $assignment->submission_type) === 'both' ? 'selected' : '' }}>File atau Link</option>
                                    </select>
                                    @error('submission_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Settings -->
                                <div id="file-settings">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_files" class="form-label">Maksimal File</label>
                                                <input type="number" class="form-control @error('max_files') is-invalid @enderror"
                                                       id="max_files" name="max_files" value="{{ old('max_files', $assignment->max_files) }}"
                                                       min="1" max="10">
                                                @error('max_files')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_file_size" class="form-label">Maksimal Ukuran File (MB)</label>
                                                <input type="number" class="form-control @error('max_file_size') is-invalid @enderror"
                                                       id="max_file_size" name="max_file_size"
                                                       value="{{ old('max_file_size', $assignment->max_file_size ? round($assignment->max_file_size / 1024 / 1024) : 50) }}"
                                                       min="1" max="1024">
                                                @error('max_file_size')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="allowed_file_types" class="form-label">Tipe File yang Diizinkan</label>
                                        <div class="row">
                                            @php
                                                $fileTypes = [
                                                    'pdf' => 'PDF', 'doc' => 'Word (DOC)', 'docx' => 'Word (DOCX)',
                                                    'xls' => 'Excel (XLS)', 'xlsx' => 'Excel (XLSX)',
                                                    'ppt' => 'PowerPoint (PPT)', 'pptx' => 'PowerPoint (PPTX)',
                                                    'txt' => 'Text', 'jpg' => 'JPEG', 'jpeg' => 'JPEG', 'png' => 'PNG',
                                                    'gif' => 'GIF', 'mp4' => 'MP4', 'mov' => 'MOV', 'avi' => 'AVI',
                                                    'mkv' => 'MKV', 'mp3' => 'MP3', 'wav' => 'WAV',
                                                    'zip' => 'ZIP', 'rar' => 'RAR'
                                                ];
                                                $currentFileTypes = old('allowed_file_types', $assignment->allowed_file_types ?? []);
                                            @endphp
                                            @foreach($fileTypes as $ext => $label)
                                                <div class="col-md-3 col-sm-4 col-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="allowed_file_types[]" value="{{ $ext }}"
                                                               id="file_{{ $ext }}"
                                                               {{ in_array($ext, $currentFileTypes) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="file_{{ $ext }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('allowed_file_types')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Schedule & Grading -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Jadwal & Penilaian</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Tenggat Waktu</label>
                                    <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror"
                                           id="due_date" name="due_date"
                                           value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="max_points" class="form-label">Poin Maksimal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_points') is-invalid @enderror"
                                           id="max_points" name="max_points" value="{{ old('max_points', $assignment->max_points) }}"
                                           min="1" max="1000" required>
                                    @error('max_points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Late Submission -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="allow_late_submission" name="allow_late_submission" value="1"
                                               {{ old('allow_late_submission', $assignment->allow_late_submission) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_late_submission">
                                            Izinkan pengumpulan terlambat
                                        </label>
                                    </div>
                                </div>

                                <div id="late-settings">
                                    <div class="mb-3">
                                        <label for="late_submission_until" class="form-label">Batas akhir pengumpulan terlambat</label>
                                        <input type="datetime-local" class="form-control @error('late_submission_until') is-invalid @enderror"
                                               id="late_submission_until" name="late_submission_until"
                                               value="{{ old('late_submission_until', $assignment->late_submission_until ? $assignment->late_submission_until->format('Y-m-d\TH:i') : '') }}">
                                        @error('late_submission_until')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="late_penalty" class="form-label">Penalti keterlambatan (%)</label>
                                        <input type="number" class="form-control @error('late_penalty') is-invalid @enderror"
                                               id="late_penalty" name="late_penalty" value="{{ old('late_penalty', $assignment->late_penalty) }}"
                                               min="0" max="100" step="0.1">
                                        @error('late_penalty')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="is_active" name="is_active" value="1"
                                               {{ old('is_active', $assignment->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Tugas aktif
                                        </label>
                                    </div>
                                </div>

                                <!-- Visibility -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="show_to_students" name="show_to_students" value="1"
                                               {{ old('show_to_students', $assignment->show_to_students) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_to_students">
                                            Tampilkan ke siswa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                                <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-outline-secondary w-100">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const submissionType = document.getElementById('submission_type');
    const fileSettings = document.getElementById('file-settings');
    const allowLateSubmission = document.getElementById('allow_late_submission');
    const lateSettings = document.getElementById('late-settings');

    // Toggle file settings based on submission type
    function toggleFileSettings() {
        const value = submissionType.value;
        if (value === 'file' || value === 'both') {
            fileSettings.style.display = 'block';
        } else {
            fileSettings.style.display = 'none';
        }
    }

    // Toggle late submission settings
    function toggleLateSettings() {
        if (allowLateSubmission.checked) {
            lateSettings.style.display = 'block';
        } else {
            lateSettings.style.display = 'none';
        }
    }

    submissionType.addEventListener('change', toggleFileSettings);
    allowLateSubmission.addEventListener('change', toggleLateSettings);

    // Initialize on page load
    toggleFileSettings();
    toggleLateSettings();

    // Convert max_file_size from MB to bytes for backend
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const maxFileSize = document.getElementById('max_file_size');
        if (maxFileSize.value) {
            maxFileSize.value = maxFileSize.value * 1024 * 1024; // Convert MB to bytes
        }
    });
});
</script>
@endpush
@endsection