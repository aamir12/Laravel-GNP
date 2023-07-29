<?php

namespace App\Http\Requests\UserAddress;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserAddressRequest extends FormRequest
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
            'name' => 'required|string',
            'phone' => 'string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'string',
            'address_line_3' => 'string',
            'town' => 'required|string',
            'county' => 'string',
            'postcode' => 'required|string',
            'country' => 'required|string',
            'delivery_instructions' => 'string',
        ];
    }
}
