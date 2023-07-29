<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class DeleteGroupRequest extends FormRequest
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
            'id' => 'required|exists:groups,id,deleted_at,NULL',
            'backup_group_id' => 'exists:groups,id,deleted_at,NULL|different:id',
            'default_group_id' => 'exists:groups,id,deleted_at,NULL|different:id',
        ];
    }
}
