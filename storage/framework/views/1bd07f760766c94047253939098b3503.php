<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['id', 'name', 'value' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['id', 'name', 'value' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div wire:ignore>
    <textarea
        id="<?php echo e($id); ?>"
        name="<?php echo e($name); ?>"
        <?php echo e($attributes->merge(['class' => 'summernote'])); ?>

    ><?php echo e($value); ?></textarea>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#<?php echo e($id); ?>').summernote({
            placeholder: 'Tuliskan konten Anda di sini...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    uploadImage(files[0], '#<?php echo e($id); ?>');
                }
            }
        });

        function uploadImage(file, editor) {
            let data = new FormData();
            data.append("image", file);
            data.append("_token", "<?php echo e(csrf_token()); ?>"); // Tambahkan CSRF token

            $.ajax({
                url: "<?php echo e(route('images.upload')); ?>",
                method: "POST",
                data: data,
                contentType: false,
                processData: false,
                success: function(response) {
                    $(editor).summernote('insertImage', response.url);
                },
                error: function(data) {
                    console.error(data);
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\PC2\Videos\IT\Code\LMSCOK\ABC\Cok\LMSAPP_Laravel_V2\resources\views/components/forms/summernote-editor.blade.php ENDPATH**/ ?>