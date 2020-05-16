<?php

namespace App\Http\Requests\User\Data;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            /** Send file attachment */
            'attachment' => [
                'required_without_all:plain_data,plain_name',
                'file',
                'max:' .config('secured_data.attachment.max_size'),
                'mimetypes:' .implode(',', config('secured_data.attachment.mimetypes'))
            ],

            /** Or plain data */
            'plain_data' => [
                'required_without_all:attachment', 
                'required_with_all:plain_name',
                'max:' .config('secured_data.plain_data.max_size')
            ],
            'plain_name' => [
                'required_without_all:attachment', 
                'required_with_all:plain_data',
                'string',
                'max:255'
            ]
        ];
    }
}
