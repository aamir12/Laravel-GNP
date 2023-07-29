<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class RemoveUsersFromGroupRequest extends FormRequest
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
            'id' => 'required|
                     exists:groups,id,deleted_at,NULL|
                     unique:groups,parent_id,NULL,id,deleted_at,NULL',
            'users' => 'required|array',
            'users.*' => 'exists:users,id|
                          distinct|
                          unique:group_user,user_id,NULL,NULL,group_id,' . $this->id
        ];
    }
}
