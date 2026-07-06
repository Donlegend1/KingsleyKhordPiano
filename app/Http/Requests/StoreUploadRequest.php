<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUploadRequest extends FormRequest
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
            'category' => 'required|in:piano exercise,extra courses,quick lessons,learn songs',
            'description' => 'nullable|string|max:1000',
            'video_url' => 'required|string',
            'level' => 'nullable|string|max:50',
            'skill_level' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'video_type' => 'nullable|string',
            'series' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'audio_resource' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480',
            'pdf_resource' => 'nullable|file|mimes:pdf|max:20480',
        ];
    }

}
