<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUploadRequest extends FormRequest
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
            'level' => 'required',
            'thumbnail' => 'nullable',
            'status' => 'nullable|in:active,inactive,draft',
            'video_url' => 'nullable|string',
            'video_type' => 'nullable|string',
            'skill_level' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'series' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
        ];
    }
}
