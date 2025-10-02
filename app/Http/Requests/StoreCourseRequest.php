<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',

            'enable_periods' => 'nullable|boolean',
            'periods' => 'nullable|array',
            'periods.*.name' => 'required_with:periods|string|max:255',
            'periods.*.start_date' => 'nullable|date',
            'periods.*.end_date' => 'nullable|date|after:periods.*.start_date',
            'periods.*.description' => 'nullable|string',
            'periods.*.max_participants' => 'nullable|integer|min:1',

            'create_default_period' => 'nullable|boolean',
            'default_start_date' => 'nullable|date',
            'default_end_date' => 'nullable|date|after:default_start_date',
        ];
    }
}

