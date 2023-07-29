<?php

namespace App\Http\Requests\Prize;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrizeRequest extends FormRequest
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
            'id' => 'required|exists:prizes,id,deleted_at,NULL',
            'name' => 'string',
            'competition_id' => 'exists:competitions,id,deleted_at,NULL',
            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'type' => 'in:cash,goods,digital',
            'amount' => 'required_if:type,cash|regex:/^\d+(\.\d{1,2})?$/',
            'currency' => 'required_if:type,cash',
            'reference' => 'nullable|alpha_num',
            'max_winners' => 'numeric|min:1'
        ];
    }
}
