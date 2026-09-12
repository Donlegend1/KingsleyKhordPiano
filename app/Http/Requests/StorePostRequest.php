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
        if ($this->input('subcategory') === 'announcement') {
            return in_array(optional(auth()->user())->role, ['admin', 'superadmin'], true);
        }

        return true;
    }

    public function rules(): array
    {
         return [
        'title'       => 'required|string|max:255',
        'blocks'        => 'required|array|min:1',
        'category'    => ['required', new Enum(PostCategoryEnum::class)],
        'subcategory' => ['required', new Enum(PostSubCategoryEnum::class)],
        'parent_post_id' => 'nullable|integer|exists:posts,id',
        'media'       => 'nullable|array|max:6', // max 6 files
        'media.*'     => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,wmv|max:51200', // 50 MB max each
       ];
    }

    /**
     * Uploaded block files aren't a fixed field name (they're referenced
     * indirectly via `blocks.*.content`), so mime/size limits can't be
     * expressed as declarative rules and are checked here instead.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allowedExtensions = [
                'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'file'  => ['pdf', 'doc', 'docx', 'txt', 'mid', 'midi'],
            ];

            foreach ($this->input('blocks', []) as $index => $block) {
                $type = $block['type'] ?? null;

                if (!in_array($type, ['image', 'file'], true)) {
                    continue;
                }

                $fieldName = $block['content'] ?? null;
                if (!$fieldName || !$this->hasFile($fieldName)) {
                    continue;
                }

                $file = $this->file($fieldName);
                $extension = strtolower($file->getClientOriginalExtension());

                if (!in_array($extension, $allowedExtensions[$type], true)) {
                    $validator->errors()->add(
                        "blocks.$index.content",
                        $type === 'image'
                            ? 'Only image files (jpg, png, gif, webp) can be attached as photos.'
                            : 'Only PDF, document, and MIDI files (pdf, doc, docx, txt, mid, midi) can be attached.'
                    );
                    continue;
                }

                if ($file->getSize() > 10 * 1024 * 1024) {
                    $validator->errors()->add("blocks.$index.content", 'File must be smaller than 10MB.');
                }
            }
        });
    }
}
