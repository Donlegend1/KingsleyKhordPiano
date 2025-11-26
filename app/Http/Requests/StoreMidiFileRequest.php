<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMidiFileRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'video_path' => 'required|string',
            'video_type' => 'required|in:youtube,google,local,iframe',
            'midi_file' => 'required|file|mimetypes:audio/midi,audio/x-midi',
            'lmv_file' => 'required|file',
            'thumbnail' => 'nullable|file',
            'description' => 'nullable|string',
        ];
    }
}
