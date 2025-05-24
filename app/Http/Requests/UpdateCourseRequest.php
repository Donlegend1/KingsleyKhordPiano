<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'level' => 'required|in:beginner,intermediate,advanced',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        
            'status' => 'nullable|in:active,inactive,draft',
            'video_url' => 'nullable|string',
            'prerequisites' => 'nullable|string',
            'what_you_will_learn' => 'nullable|string',
            'published_at' => 'nullable|date',
        ];
    }
}
