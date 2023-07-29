<?php

namespace App\Http\Requests\Prize;

use Illuminate\Foundation\Http\FormRequest;

class BulkCreatePrizeRequest extends FormRequest
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
            'prizes' => 'required',
            'prizes.*.name' => 'required|string',
            'prizes.*.competition_id' => 'required|exists:competitions,id,deleted_at,NULL',
            'prizes.*.image' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'prizes.*.type' => 'required|in:cash,goods,digital',
            'prizes.*.amount' => 'required_if:prizes.*.type,cash|regex:/^\d+(\.\d{1,2})?$/',
            'prizes.*.currency' => 'required_if:prizes.*.type,cash',
            'prizes.*.reference' => 'nullable|alpha_num',
            'prizes.*.max_winners' => 'numeric|min:1'
        ];
    }
}
