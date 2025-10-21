


<?php if (! $__env->hasRenderedOnce('ce207a2a-70a8-4495-8e41-c4c1a3af8c3d')): $__env->markAsRenderedOnce('ce207a2a-70a8-4495-8e41-c4c1a3af8c3d'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
/**
 * Initialize Summernote with File Manager Button
 * @param {string} selector - CSS selector for textarea
 * @param {object} options - Summernote options
 */
function initSummernoteWithFileManager(selector, options = {}) {
    // Wait for jQuery and Summernote to be loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    if (typeof jQuery.fn.summernote === 'undefined') {
        console.error('Summernote is not loaded');
        return;
    }

    // Default options
    const defaultOptions = {
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']],
            ['filemanager', ['filemanager']] // Custom button
        ],
        buttons: {
            filemanager: function(context) {
                const ui = $.summernote.ui;

                const button = ui.button({
                    contents: '<i class="fas fa-folder-open"></i> File Manager',
                    tooltip: 'Open File Manager',
                    click: function() {
                        // Check if openFileManager exists
                        if (typeof openFileManager === 'function') {
                            openFileManager(function(url) {
                                // Insert image at cursor position
                                context.invoke('editor.insertImage', url);
                            });
                        } else {
                            alert('File Manager is not loaded. Please include file-manager/modal.blade.php');
                        }
                    }
                });

                return button.render();
            }
        },
        callbacks: {
            onImageUpload: function(files) {
                // Upload via standard method
                uploadImageToServer(files[0], $(this));
            }
        }
    };

    // Merge custom options with defaults
    const mergedOptions = $.extend(true, {}, defaultOptions, options);

    // Initialize Summernote
    $(selector).summernote(mergedOptions);
}

/**
 * Upload image to server (standard method)
 */
function uploadImageToServer(file, editor) {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');

    $.ajax({
        url: '<?php echo e(route('images.upload')); ?>',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.url) {
                editor.summernote('insertImage', response.url);
            }
        },
        error: function(xhr) {
            console.error('Upload failed:', xhr);
            alert('Failed to upload image');
        }
    });
}

/**
 * Initialize standard Summernote without File Manager
 */
function initSummernoteStandard(selector, options = {}) {
    if (typeof jQuery === 'undefined' || typeof jQuery.fn.summernote === 'undefined') {
        console.error('jQuery or Summernote is not loaded');
        return;
    }

    const defaultOptions = {
        height: 300,
        callbacks: {
            onImageUpload: function(files) {
                uploadImageToServer(files[0], $(this));
            }
        }
    };

    const mergedOptions = $.extend(true, {}, defaultOptions, options);
    $(selector).summernote(mergedOptions);
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Custom style for File Manager button in Summernote */
.note-btn-group .note-btn.btn-filemanager {
    background-color: #7f1d1d !important;
    color: white !important;
    border-color: #7f1d1d !important;
}

.note-btn-group .note-btn.btn-filemanager:hover {
    background-color: #991b1b !important;
    border-color: #991b1b !important;
}
</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/file-manager/summernote.blade.php ENDPATH**/ ?>