<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReflectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
            'mood' => ['nullable', 'in:very_sad,sad,neutral,happy,very_happy'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50', 'regex:/^[a-zA-Z0-9\s\-_]+$/'],
            'visibility' => ['required', 'in:private,instructors_only,public'],
            'requires_response' => ['boolean']
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul refleksi harus diisi.',
            'title.max' => 'Judul refleksi tidak boleh lebih dari 255 karakter.',
            'content.required' => 'Isi refleksi harus diisi.',
            'content.min' => 'Isi refleksi minimal 10 karakter.',
            'content.max' => 'Isi refleksi tidak boleh lebih dari 5000 karakter.',
            'mood.in' => 'Mood yang dipilih tidak valid.',
            'tags.max' => 'Maksimal 10 tag diperbolehkan.',
            'tags.*.max' => 'Setiap tag maksimal 50 karakter.',
            'tags.*.regex' => 'Tag hanya boleh berisi huruf, angka, spasi, tanda minus, dan underscore.',
            'visibility.required' => 'Pengaturan visibilitas harus dipilih.',
            'visibility.in' => 'Pengaturan visibilitas tidak valid.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'requires_response' => $this->boolean('requires_response'),
            'tags' => $this->input('tags', [])
        ]);
    }

    /**
     * Get the validated data with additional processing.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Add user_id automatically
        $validated['user_id'] = auth()->id();

        // Clean up tags
        if (isset($validated['tags']) && is_array($validated['tags'])) {
            $validated['tags'] = array_unique(array_filter(array_map('trim', $validated['tags'])));
        }

        return $validated;
    }
}