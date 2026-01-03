<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\Users\UserTypeEnum;

class StoreEmailCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],

            'body' => ['required', 'string'],

            'targets' => ['required', 'array', 'min:1'],

            'targets.*' => [
                'required',
                'string',
                Rule::in([
                    UserTypeEnum::PREMIUM->value,
                    UserTypeEnum::STANDARD->value,
                    UserTypeEnum::VISITOR->value,
                ]),
            ],

            'status' => ['required', 'string', Rule::in(['draft', 'sent'])],
        ];
    }
}
