<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompetitionRequest extends FormRequest
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
            'id' => [
                'required',
                Rule::exists('competitions')->where(function ($query) {
                    $query->where('state', '!=', 'ended');
                }),
            ],
            'name' => 'string',
            'description' => 'string',
            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'type' => 'in:Fixed,Rolling',
            'score_threshold' => 'regex:/^\d+(\.\d{1,2})?$/',
            'threshold_operator' => 'in:>,>=,==,<=,<',
            'start_date' => 'date|date_format:Y-m-d H:i|after:' . now()->format('Y-m-d H:i'),
            'end_date' => 'not_present_with:period|date|date_format:Y-m-d H:i|after:start_date',
            'period' => 'not_present_with:end_date|in:daily,weekly,monthly',
            'is_lottery' => 'boolean',
            'space_count' => 'integer|min:0',
            'entry_fee' => 'regex:/^\d+(\.\d{1,2})?$/',
            'groups' => 'array',
            'groups.*' => 'distinct|exists:groups,id',
            'auto_enter_user' => 'boolean',
            'metadata' => 'json',
            'status' =>'in:draft,live,archived'
        ];
    }
}
