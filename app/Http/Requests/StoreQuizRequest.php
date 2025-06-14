<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function rules(): array
    {
        return [
          
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required',
            'thumbnail' => 'required|image|max:2048',
            'main_audio' => 'required|file|mimes:mp3,wav|max:10240',
            'questions' => 'required|array|min:1',
            'questions.*.audio' => 'required|file|mimes:mp3,wav|max:10240',
            'questions.*.correct_option' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
