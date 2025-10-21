<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('File Manager Test')); ?>

        </h2>
     <?php $__env->endSlot(); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header" style="background: linear-gradient(to right, #7f1d1d, #991b1b); color: white;">
                    <h4 class="mb-0">Test File Manager dengan Summernote</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Petunjuk:</strong>
                        <ul class="mb-0">
                            <li>Klik tombol "File Manager" di toolbar Summernote untuk membuka file browser</li>
                            <li>Atau drag & drop gambar langsung ke editor</li>
                            <li>Test upload dan delete file</li>
                        </ul>
                    </div>

                    <form>
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Title</label>
                            <input type="text" class="form-control" id="title" placeholder="Enter title">
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label fw-bold">Content</label>
                            <textarea id="content" name="content"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="history.back()">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">How to Implement</h5>
                </div>
                <div class="card-body">
                    <h6>Step 1: Include File Manager Modal</h6>
                    <pre class="bg-light p-3 rounded"><code><?php echo e('@include(\'file-manager.modal\')'); ?></code></pre>

                    <h6 class="mt-3">Step 2: Include Summernote Integration</h6>
                    <pre class="bg-light p-3 rounded"><code><?php echo e('@include(\'file-manager.summernote\')'); ?></code></pre>

                    <h6 class="mt-3">Step 3: Initialize in Script</h6>
                    <pre class="bg-light p-3 rounded"><code>&lt;script&gt;
$(document).ready(function() {
    initSummernoteWithFileManager('#content', {
        height: 400
    });
});
&lt;/script&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

 @endComponentClass


<?php echo $__env->make('file-manager.modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('file-manager.summernote', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Wait for DOM and all scripts to load
$(document).ready(function() {
    // Small delay to ensure all dependencies are loaded
    setTimeout(function() {
        console.log('Initializing Summernote with File Manager...');

        initSummernoteWithFileManager('#content', {
            height: 400,
            placeholder: 'Write your content here...'
        });

        console.log('Summernote initialized!');
    }, 100);
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/file-manager/test.blade.php ENDPATH**/ ?>