<?php

namespace App\Http\Requests\Comments;

use App\Http\Requests\BaseFormRequest;
use App\Utils\Helpers\ResponseHelpers;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpsertReplyRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'command' => 'required',
            'parent_record_id' => 'required',
            'record_id' => 'required',
            'description'=>'required|string '
        ];
    }

    /**
     * @param Validator $validator
     * @return mixed
     */
    public function failedValidation(Validator $validator)
    {
        $errorMessages = implode('. ', $validator->errors()->all());
        throw new HttpResponseException(ResponseHelpers::ConvertToJsonResponseWrapper(
            $validator->errors(),
            "Validation errors: " . $errorMessages,
            422
        ));
    }
}
