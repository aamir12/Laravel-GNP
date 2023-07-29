<?php

namespace App\Http\Requests\League;

use Illuminate\Foundation\Http\FormRequest;

class CreatePrivateLeagueRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'score_aggregation_period' => 'in:daily,monthly,weekly',
            'group_id' => 'exists:groups,id,deleted_at,NULL',
            'metadata' => 'json'
        ];
    }
}
