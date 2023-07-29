<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class BulkCreateUsersRequest extends FormRequest
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
            'users' => 'required|array',
            'users.*.email' => 'required_without:users.*.external_id|
                                email|
                                distinct:ignore_case|
                                nullable|
                                unique:users,email',
            'users.*.external_id' => 'required_without:users.*.email|
                                      distinct:ignore_case|
                                      unique:users,external_id',
            'users.*.first_name' => 'alpha',
            'users.*.last_name' => 'alpha',
            'users.*.phone' => 'numeric',
            'users.*.dob' => 'date|date_format:Y-m-d|before:' . now()->format('Y-m-d'),
            'users.*.groups' => 'array',
            'users.*.groups.*' => 'distinct|
                                   exists:groups,id,deleted_at,NULL|
                                   unique:groups,parent_id,NULL,id,deleted_at,NULL',
            'users.*.metadata' => 'json',
        ];
    }
}
