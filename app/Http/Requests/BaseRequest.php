<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function commonRules()
    {
        return [
            'page_size' => 'integer|min:1',
            'page_number' => 'integer|min:1',
            'order_by' => 'nullable|string',
            'search_term' => 'nullable',
        ];
    }
}
