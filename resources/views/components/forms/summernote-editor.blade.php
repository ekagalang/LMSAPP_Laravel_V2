@props(['id', 'name', 'value' => ''])

<div wire:ignore>
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'summernote']) }}
    >{{ $value }}</textarea>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#{{ $id }}').summernote({
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
                    uploadImage(files[0], '#{{ $id }}');
                }
            }
        });

        function uploadImage(file, editor) {
            let data = new FormData();
            data.append("image", file);
            data.append("_token", "{{ csrf_token() }}"); // Tambahkan CSRF token

            $.ajax({
                url: "{{ route('images.upload') }}",
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
@endpush
