<?php

namespace App\Http\Requests\UserAddress;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserAddressRequest extends FormRequest
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
            'id' => 'required|exists:user_addresses,id,user_id,' . Auth::id(),
            'name' => 'string',
            'phone' => 'string',
            'address_line_1' => 'string',
            'address_line_2' => 'string',
            'address_line_3' => 'string',
            'town' => 'string',
            'county' => 'string',
            'postcode' => 'string',
            'country' => 'string',
            'delivery_instructions' => 'string',
        ];
    }
}
