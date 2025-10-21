
<?php if (! $__env->hasRenderedOnce('ea3f308e-d275-4cf6-b171-99f459e96ce2')): $__env->markAsRenderedOnce('ea3f308e-d275-4cf6-b171-99f459e96ce2'); ?>
<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<!-- File Manager Modal -->
<div class="modal fade" id="fileManagerModal" tabindex="-1" aria-labelledby="fileManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(to right, #7f1d1d, #991b1b); color: white;">
                <h5 class="modal-title" id="fileManagerModalLabel">
                    <i class="fas fa-folder-open me-2"></i>File Manager
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Upload Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Upload File</h6>
                    </div>
                    <div class="card-body">
                        <form id="fileUploadForm" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-9">
                                    <input type="file" class="form-control" id="fileInput" name="file" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                    <small class="text-muted">Allowed: Images, PDF, Word, Excel, PowerPoint, ZIP (Max: 10MB)</small>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-cloud-upload-alt me-2"></i>Upload
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div id="uploadProgress" class="mt-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchFiles" placeholder="Search files...">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="filterType">
                                <option value="">All Files</option>
                                <option value="image">Images Only</option>
                                <option value="document">Documents Only</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Files Grid -->
                <div id="filesContainer" class="row g-3">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5" style="display: none;">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No files found</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- File Item Template -->
<template id="fileItemTemplate">
    <div class="col-md-3 col-sm-4 col-6 file-item" data-filename="" data-type="">
        <div class="card h-100 shadow-sm file-card">
            <div class="position-relative">
                <div class="file-preview bg-light d-flex align-items-center justify-content-center" style="height: 150px; cursor: pointer;">
                    <!-- Image or icon will be inserted here -->
                </div>
                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 delete-file" data-path="">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body p-2">
                <p class="file-name mb-1 small text-truncate" title=""></p>
                <small class="text-muted file-size"></small>
            </div>
        </div>
    </div>
</template>

<style>
.file-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.file-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.file-preview {
    overflow: hidden;
}

.file-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-icon {
    font-size: 4rem;
    color: #6c757d;
}

.delete-file {
    opacity: 0.7;
    transition: opacity 0.2s;
}

.delete-file:hover {
    opacity: 1;
}
</style>

<script>
let currentFiles = [];
let fileManagerCallback = null;

// Open file manager with callback
function openFileManager(callback) {
    fileManagerCallback = callback;
    const modal = new bootstrap.Modal(document.getElementById('fileManagerModal'));
    modal.show();
    loadFiles();
}

// Load files
function loadFiles() {
    fetch('<?php echo e(route('file-manager.files')); ?>', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(files => {
        currentFiles = files;
        displayFiles(files);
    })
    .catch(error => {
        console.error('Error loading files:', error);
        showError('Failed to load files');
    });
}

// Display files
function displayFiles(files) {
    const container = document.getElementById('filesContainer');
    const emptyState = document.getElementById('emptyState');

    container.innerHTML = '';

    if (files.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }

    container.style.display = 'flex';
    emptyState.style.display = 'none';

    files.forEach(file => {
        const fileCard = createFileCard(file);
        container.appendChild(fileCard);
    });
}

// Create file card
function createFileCard(file) {
    const template = document.getElementById('fileItemTemplate');
    const clone = template.content.cloneNode(true);
    const div = clone.querySelector('.file-item');

    div.dataset.filename = file.name.toLowerCase();
    div.dataset.type = file.is_image ? 'image' : 'document';

    const preview = clone.querySelector('.file-preview');
    if (file.is_image) {
        preview.innerHTML = `<img src="${file.url}" alt="${file.name}">`;
    } else {
        const icon = getFileIcon(file.extension);
        preview.innerHTML = `<i class="${icon} file-icon"></i>`;
    }

    // Click to select file
    preview.addEventListener('click', () => selectFile(file));

    clone.querySelector('.file-name').textContent = file.name;
    clone.querySelector('.file-name').title = file.name;
    clone.querySelector('.file-size').textContent = formatFileSize(file.size);

    const deleteBtn = clone.querySelector('.delete-file');
    deleteBtn.dataset.path = file.path;
    deleteBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        deleteFile(file.path, file.name);
    });

    return clone;
}

// Select file
function selectFile(file) {
    if (fileManagerCallback) {
        fileManagerCallback(file.url);
        bootstrap.Modal.getInstance(document.getElementById('fileManagerModal')).hide();
    }
}

// Get file icon
function getFileIcon(extension) {
    const icons = {
        'pdf': 'fas fa-file-pdf text-danger',
        'doc': 'fas fa-file-word text-primary',
        'docx': 'fas fa-file-word text-primary',
        'xls': 'fas fa-file-excel text-success',
        'xlsx': 'fas fa-file-excel text-success',
        'ppt': 'fas fa-file-powerpoint text-warning',
        'pptx': 'fas fa-file-powerpoint text-warning',
        'zip': 'fas fa-file-archive text-secondary',
        'rar': 'fas fa-file-archive text-secondary'
    };
    return icons[extension] || 'fas fa-file text-muted';
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Upload file
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('fileUploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!file) {
                showError('Please select a file');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const progressBar = document.querySelector('#uploadProgress .progress-bar');
            document.getElementById('uploadProgress').style.display = 'block';

            fetch('<?php echo e(route('file-manager.upload')); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('File uploaded successfully');
                    fileInput.value = '';
                    loadFiles();
                } else {
                    showError(data.message || 'Upload failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Upload failed');
            })
            .finally(() => {
                document.getElementById('uploadProgress').style.display = 'none';
                progressBar.style.width = '0%';
            });
        });
    }

    // Search files
    document.getElementById('searchFiles')?.addEventListener('input', function(e) {
        filterFiles();
    });

    // Filter by type
    document.getElementById('filterType')?.addEventListener('change', function(e) {
        filterFiles();
    });
});

// Filter files
function filterFiles() {
    const searchTerm = document.getElementById('searchFiles').value.toLowerCase();
    const filterType = document.getElementById('filterType').value;

    const fileItems = document.querySelectorAll('.file-item');
    let visibleCount = 0;

    fileItems.forEach(item => {
        const filename = item.dataset.filename;
        const type = item.dataset.type;

        let matchSearch = filename.includes(searchTerm);
        let matchType = !filterType || type === filterType;

        if (matchSearch && matchType) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Show empty state if no results
    const container = document.getElementById('filesContainer');
    const emptyState = document.getElementById('emptyState');

    if (visibleCount === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
    } else {
        container.style.display = 'flex';
        emptyState.style.display = 'none';
    }
}

// Delete file
function deleteFile(path, filename) {
    if (!confirm(`Are you sure you want to delete "${filename}"?`)) {
        return;
    }

    fetch('<?php echo e(route('file-manager.delete')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ path: path })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('File deleted successfully');
            loadFiles();
        } else {
            showError(data.message || 'Delete failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Delete failed');
    });
}

// Show success message
function showSuccess(message) {
    // You can integrate with your existing notification system
    alert(message);
}

// Show error message
function showError(message) {
    // You can integrate with your existing notification system
    alert(message);
}
</script>
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/file-manager/index.blade.php ENDPATH**/ ?>