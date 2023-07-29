<?php

namespace App\Http\Requests\Branding;

use Illuminate\Foundation\Http\FormRequest;
use LVR\Colour\Hex;

class UpdateBrandingRequest extends FormRequest
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
            'company_name' => '',
            'primary_color' => [new Hex],
            'company_address' => '',
            'logo' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'terms_url' => '',
            'privacy_url' => '',
            'support_email' => '',
        ];
    }
}
