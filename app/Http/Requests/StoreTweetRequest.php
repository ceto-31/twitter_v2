<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTweetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all logged-in users to use this
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:280',
        ];
    }
}
