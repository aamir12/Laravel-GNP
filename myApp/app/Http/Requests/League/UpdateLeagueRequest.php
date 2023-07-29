<?php

namespace App\Http\Requests\League;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeagueRequest extends FormRequest
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
            'id' => 'required|exists:leagues,id,deleted_at,NULL',
            'name' => 'string',
            'description' => 'string',
            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'parent_id' => 'exists:leagues,id,deleted_at,NULL',
            'score_aggregation_period' => 'in:daily,monthly,weekly',
            'group_id' => 'exists:groups,id,deleted_at,NULL',
            'metadata' => 'json'
        ];
    }
}
