<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMidiFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'video_path' => 'sometimes|string',
            'video_type' => 'sometimes|in:youtube,google,local,iframe',
            'midi_file' => 'sometimes|file',
            'lmv_file' => 'sometimes|file',
            'thumbnail' => 'sometimes|file|image|max:2048',
            'description' => 'sometimes|string|nullable',
        ];
    }
}
