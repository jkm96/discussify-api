<?php

namespace App\Http\Requests\Posts;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class FetchPostsRequest extends BaseRequest
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
    public function rules(): array
    {
        return $this->commonRules() + [
                'fetch' => 'nullable',
                'period_from' => 'nullable',
                'period_to' => 'nullable',
            ];
    }
}
