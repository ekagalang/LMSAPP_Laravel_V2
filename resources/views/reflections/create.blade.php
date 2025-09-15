@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('reflections.index') }}"
               class="text-purple-600 hover:text-purple-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">✨ Tulis Refleksi Baru</h1>
                <p class="text-gray-600">Bagikan pemikiran dan pengalaman pembelajaran Anda</p>
            </div>
        </div>
    </div>

    <form action="{{ route('reflections.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Judul Refleksi
                </label>
                <input type="text" id="title" name="title"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Berikan judul yang menarik untuk refleksi Anda..."
                       value="{{ old('title') }}" required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Isi Refleksi
                </label>
                <textarea id="content" name="content" rows="8"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                          placeholder="Tuliskan refleksi Anda tentang pengalaman pembelajaran, tantangan yang dihadapi, pencapaian, atau insight yang didapat..."
                          required>{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">
                    💡 Tips: Tulis dengan jujur tentang apa yang Anda rasakan dan pelajari. Refleksi yang baik membantu Anda dan instruktur memahami perjalanan pembelajaran Anda.
                </p>
            </div>

            <!-- Mood Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                    </svg>
                    Bagaimana perasaan Anda? (Opsional)
                </label>
                <div class="grid grid-cols-5 gap-4">
                    <label class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                        <input type="radio" name="mood" value="very_sad" class="sr-only" {{ old('mood') === 'very_sad' ? 'checked' : '' }}>
                        <span class="text-3xl mb-2">😢</span>
                        <span class="text-xs text-gray-600">Sangat Sedih</span>
                    </label>
                    <label class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                        <input type="radio" name="mood" value="sad" class="sr-only" {{ old('mood') === 'sad' ? 'checked' : '' }}>
                        <span class="text-3xl mb-2">😔</span>
                        <span class="text-xs text-gray-600">Sedih</span>
                    </label>
                    <label class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                        <input type="radio" name="mood" value="neutral" class="sr-only" {{ old('mood') === 'neutral' ? 'checked' : '' }}>
                        <span class="text-3xl mb-2">😐</span>
                        <span class="text-xs text-gray-600">Netral</span>
                    </label>
                    <label class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                        <input type="radio" name="mood" value="happy" class="sr-only" {{ old('mood') === 'happy' ? 'checked' : '' }}>
                        <span class="text-3xl mb-2">😊</span>
                        <span class="text-xs text-gray-600">Senang</span>
                    </label>
                    <label class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 transition-colors">
                        <input type="radio" name="mood" value="very_happy" class="sr-only" {{ old('mood') === 'very_happy' ? 'checked' : '' }}>
                        <span class="text-3xl mb-2">😄</span>
                        <span class="text-xs text-gray-600">Sangat Senang</span>
                    </label>
                </div>
                @error('mood')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tags -->
            <div class="mb-6">
                <label for="tags-input" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Tag (Opsional)
                </label>
                <input type="text" id="tags-input"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Tambahkan tag untuk mengelompokkan refleksi (misal: pembelajaran, tantangan, pencapaian)"
                       onkeypress="addTag(event)">
                <div id="tags-container" class="mt-2 flex flex-wrap gap-2"></div>
                <p class="mt-1 text-sm text-gray-500">Tekan Enter untuk menambahkan tag</p>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Visibility Settings -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Siapa yang bisa melihat refleksi ini?
                </label>
                <div class="space-y-3">
                    <label class="flex items-start space-x-3">
                        <input type="radio" name="visibility" value="instructors_only"
                               class="mt-1 text-purple-600 focus:ring-purple-500"
                               {{ old('visibility', 'instructors_only') === 'instructors_only' ? 'checked' : '' }}>
                        <div>
                            <span class="font-medium text-gray-700">Hanya Instruktur</span>
                            <p class="text-sm text-gray-500">Hanya instruktur dan admin yang bisa melihat (Disarankan)</p>
                        </div>
                    </label>
                    <label class="flex items-start space-x-3">
                        <input type="radio" name="visibility" value="public"
                               class="mt-1 text-purple-600 focus:ring-purple-500"
                               {{ old('visibility') === 'public' ? 'checked' : '' }}>
                        <div>
                            <span class="font-medium text-gray-700">Publik</span>
                            <p class="text-sm text-gray-500">Semua orang di platform ini bisa melihat</p>
                        </div>
                    </label>
                    <label class="flex items-start space-x-3">
                        <input type="radio" name="visibility" value="private"
                               class="mt-1 text-purple-600 focus:ring-purple-500"
                               {{ old('visibility') === 'private' ? 'checked' : '' }}>
                        <div>
                            <span class="font-medium text-gray-700">Pribadi</span>
                            <p class="text-sm text-gray-500">Hanya Anda yang bisa melihat</p>
                        </div>
                    </label>
                </div>
                @error('visibility')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Request Response -->
            <div class="mb-6">
                <label class="flex items-start space-x-3">
                    <input type="checkbox" name="requires_response" value="1"
                           class="mt-1 text-purple-600 focus:ring-purple-500"
                           {{ old('requires_response') ? 'checked' : '' }}>
                    <div>
                        <span class="font-medium text-gray-700">Minta respon dari instruktur</span>
                        <p class="text-sm text-gray-500">Centang jika Anda ingin instruktur memberikan respon atau feedback pada refleksi ini</p>
                    </div>
                </label>
                @error('requires_response')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('reflections.index') }}"
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-colors shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Refleksi
            </button>
        </div>
    </form>
</div>

<style>
.mood-option input:checked + .mood-content {
    @apply border-purple-500 bg-purple-50;
}
</style>

<script>
let tags = [];

function addTag(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const input = event.target;
        const tagValue = input.value.trim();

        if (tagValue && !tags.includes(tagValue)) {
            tags.push(tagValue);
            updateTagsDisplay();
            input.value = '';
        }
    }
}

function removeTag(tagToRemove) {
    tags = tags.filter(tag => tag !== tagToRemove);
    updateTagsDisplay();
}

function updateTagsDisplay() {
    const container = document.getElementById('tags-container');
    container.innerHTML = '';

    tags.forEach(tag => {
        const tagElement = document.createElement('span');
        tagElement.className = 'inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full';
        tagElement.innerHTML = `
            #${tag}
            <button type="button" onclick="removeTag('${tag}')" class="ml-2 text-purple-600 hover:text-purple-800">
                ×
            </button>
        `;
        container.appendChild(tagElement);

        // Add hidden input for form submission
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'tags[]';
        hiddenInput.value = tag;
        container.appendChild(hiddenInput);
    });
}

// Style radio buttons for mood
document.addEventListener('DOMContentLoaded', function() {
    const moodRadios = document.querySelectorAll('input[name="mood"]');
    moodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            moodRadios.forEach(r => {
                r.closest('label').classList.remove('border-purple-500', 'bg-purple-50');
                r.closest('label').classList.add('border-gray-200');
            });
            if (this.checked) {
                this.closest('label').classList.remove('border-gray-200');
                this.closest('label').classList.add('border-purple-500', 'bg-purple-50');
            }
        });
    });
});
</script>
@endsection