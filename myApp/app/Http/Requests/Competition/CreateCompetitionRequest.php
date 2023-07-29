<?php

namespace App\Http\Requests\Competition;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompetitionRequest extends FormRequest
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
            'image' => 'required|image|max:10000',
            'type' => 'in:Fixed,Rolling',
            'is_lottery' => 'boolean',
            'period' => 'not_present_with:end_date|in:daily,weekly,monthly',
            'start_date' => 'required|date|date_format:Y-m-d H:i|after:' . now()->format('Y-m-d H:i'),
            'end_date' => 'not_present_with:period|date|date_format:Y-m-d H:i|after:start_date',
            'status' => 'required|in:draft,live,archived',
            'score_threshold' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'threshold_operator' => 'in:>,>=,==,<=,<',
            'space_count' => 'integer|min:0',
            'entry_fee' => 'regex:/^\d+(\.\d{1,2})?$/',
            'auto_enter_user' => 'boolean',
            'groups' => 'array',
            'groups.*' => 'exists:groups,id',
            'metadata' => 'json',
        ];
    }
}
