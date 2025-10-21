


<?php if (! $__env->hasRenderedOnce('c1e76d6c-ad37-4315-b4d0-027f9dcb8202')): $__env->markAsRenderedOnce('c1e76d6c-ad37-4315-b4d0-027f9dcb8202'); ?>
<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.file-manager-card {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}
.file-manager-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
.file-preview {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    overflow: hidden;
    position: relative;
}
.file-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.file-icon {
    font-size: 3rem;
    color: #6c757d;
}
.delete-file-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    opacity: 0.8;
    z-index: 10;
}
.delete-file-btn:hover {
    opacity: 1;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>

<script>
if (typeof jQuery === 'undefined' || !jQuery.ajax) {
    document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<!-- File Manager Modal -->
<div class="modal fade" id="fileManagerModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(to right, #7f1d1d, #991b1b); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-folder-open me-2"></i>File Manager
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Upload Section -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Upload File</h6>
                    </div>
                    <div class="card-body">
                        <form id="fileUploadForm">
                            <?php echo csrf_field(); ?>
                            <div class="row g-2">
                                <div class="col-md-9">
                                    <input type="file" class="form-control" id="fileInput" name="file" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                                    <small class="text-muted">Allowed: Images, PDF, Word, Excel (Max: 10MB)</small>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-cloud-upload-alt me-2"></i>Upload
                                    </button>
                                </div>
                            </div>
                            <div id="uploadProgress" class="mt-2" style="display: none;">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="row g-2 mb-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="searchFiles" placeholder="Search files...">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filterType">
                            <option value="">All Files</option>
                            <option value="image">Images Only</option>
                            <option value="document">Documents Only</option>
                        </select>
                    </div>
                </div>

                <!-- Files Grid -->
                <div id="filesContainer" class="row g-3" style="max-height: 400px; overflow-y: auto;">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Loading files...</p>
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

<script>
// Global variables
let fileManagerCallback = null;
let currentFiles = [];

// Open file manager
function openFileManager(callback) {
    fileManagerCallback = callback;
    var modal = new bootstrap.Modal(document.getElementById('fileManagerModal'));
    modal.show();
    loadFileManagerFiles();
}

// Load files
function loadFileManagerFiles() {
    fetch('<?php echo e(route("file-manager.files")); ?>', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(files => {
        currentFiles = files;
        displayFileManagerFiles(files);
    })
    .catch(error => {
        console.error('Error loading files:', error);
        document.getElementById('filesContainer').innerHTML =
            '<div class="col-12 text-center py-5"><p class="text-danger">Failed to load files</p></div>';
    });
}

// Display files
function displayFileManagerFiles(files) {
    const container = document.getElementById('filesContainer');
    const emptyState = document.getElementById('emptyState');

    container.innerHTML = '';

    if (!files || files.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }

    container.style.display = 'flex';
    emptyState.style.display = 'none';

    files.forEach(file => {
        const col = document.createElement('div');
        col.className = 'col-md-3 col-sm-4 col-6 file-item';
        col.dataset.filename = file.name.toLowerCase();
        col.dataset.type = file.is_image ? 'image' : 'document';

        let previewHtml = '';
        if (file.is_image) {
            previewHtml = `<img src="${file.url}" alt="${file.name}">`;
        } else {
            const icon = getFileManagerIcon(file.extension);
            previewHtml = `<i class="${icon} file-icon"></i>`;
        }

        col.innerHTML = `
            <div class="card h-100 file-manager-card">
                <div class="file-preview" onclick="selectFileManagerFile('${file.url}')">
                    ${previewHtml}
                    <button class="btn btn-danger btn-sm delete-file-btn" onclick="event.stopPropagation(); deleteFileManager('${file.path}', '${file.name}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body p-2">
                    <p class="mb-1 small text-truncate" title="${file.name}">${file.name}</p>
                    <small class="text-muted">${formatFileManagerSize(file.size)}</small>
                </div>
            </div>
        `;

        container.appendChild(col);
    });
}

// Select file
function selectFileManagerFile(url) {
    if (fileManagerCallback) {
        fileManagerCallback(url);
        bootstrap.Modal.getInstance(document.getElementById('fileManagerModal')).hide();
    }
}

// Get file icon
function getFileManagerIcon(ext) {
    const icons = {
        'pdf': 'fas fa-file-pdf text-danger',
        'doc': 'fas fa-file-word text-primary',
        'docx': 'fas fa-file-word text-primary',
        'xls': 'fas fa-file-excel text-success',
        'xlsx': 'fas fa-file-excel text-success',
        'ppt': 'fas fa-file-powerpoint text-warning',
        'pptx': 'fas fa-file-powerpoint text-warning'
    };
    return icons[ext] || 'fas fa-file text-muted';
}

// Format file size
function formatFileManagerSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Upload file
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('fileUploadForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!file) {
                alert('Please select a file');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '<?php echo e(csrf_token()); ?>');

            document.getElementById('uploadProgress').style.display = 'block';

            fetch('<?php echo e(route("file-manager.upload")); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('File uploaded successfully');
                    fileInput.value = '';
                    loadFileManagerFiles();
                } else {
                    alert(data.message || 'Upload failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed');
            })
            .finally(() => {
                document.getElementById('uploadProgress').style.display = 'none';
            });
        });
    }

    // Search
    const searchInput = document.getElementById('searchFiles');
    if (searchInput) {
        searchInput.addEventListener('input', filterFileManagerFiles);
    }

    // Filter
    const filterSelect = document.getElementById('filterType');
    if (filterSelect) {
        filterSelect.addEventListener('change', filterFileManagerFiles);
    }
});

// Filter files
function filterFileManagerFiles() {
    const searchTerm = document.getElementById('searchFiles').value.toLowerCase();
    const filterType = document.getElementById('filterType').value;

    const items = document.querySelectorAll('.file-item');
    let visibleCount = 0;

    items.forEach(item => {
        const filename = item.dataset.filename;
        const type = item.dataset.type;

        const matchSearch = filename.includes(searchTerm);
        const matchType = !filterType || type === filterType;

        if (matchSearch && matchType) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

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
function deleteFileManager(path, filename) {
    if (!confirm(`Delete "${filename}"?`)) {
        return;
    }

    fetch('<?php echo e(route("file-manager.delete")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ path: path })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('File deleted successfully');
            loadFileManagerFiles();
        } else {
            alert(data.message || 'Delete failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Delete failed');
    });
}
</script>
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/file-manager/modal.blade.php ENDPATH**/ ?>