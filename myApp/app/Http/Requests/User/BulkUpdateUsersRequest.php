<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateUsersRequest extends FormRequest
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
            'users.*.id' => 'required|distinct|exists:users,id',
            'users.*.email' => 'email|distinct:ignore_case|unique:users,email',
            'users.*.external_id' => 'nullable|
                                      alpha_num|
                                      max:255|
                                      valid_user_external_id',
            'users.*.first_name' => 'nullable|alpha',
            'users.*.last_name' => 'nullable|alpha',
            'users.*.phone' => 'nullable|regex:/(^[+][0-9]+$)+/',
            'users.*.timezone' => 'nullable|max:50',
            'users.*.dob' => 'nullable|date|date_format:Y-m-d|before:' . now()->format('Y-m-d'),
            'users.*.roles' => 'array',
            'users.*.roles.*' => 'nullable|exists:roles,id',
            'users.*.groups' => 'array',
            'users.*.groups.*' => 'distinct|
                                   exists:groups,id,deleted_at,NULL|
                                   unique:groups,parent_id,NULL,id,deleted_at,NULL',
            'users.*.metadata' => 'json',
        ];
    }
}
