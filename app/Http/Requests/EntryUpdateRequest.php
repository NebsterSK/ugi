<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntryUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => [
                'nullable',
                'sometimes',
                'string',
                'max:255',
            ],
        ];
    }
}
