<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\PostCategoryEnum;
use App\Enums\PostSubCategoryEnum;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
        'title'       => 'nullable|string|max:255',
        'body'        => 'required|string',
        'category'    => ['required', new Enum(PostCategoryEnum::class)],
        'subcategory' => ['required', new Enum(PostSubCategoryEnum::class)],
        'media'       => 'nullable|array|max:6', // max 6 files
        'media.*'     => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,wmv|max:51200', // 50 MB max each
       ];
    }
}
