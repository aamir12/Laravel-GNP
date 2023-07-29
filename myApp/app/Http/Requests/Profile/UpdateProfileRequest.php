<?php

namespace App\Http\Requests\Profile;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $userId = Auth::id();
        return [
            'email' => 'email|unique:users,email,' . $userId,
            'username' => 'alpha_dash|unique:users,username,' . $userId,
            'paypal_email' => 'email',
            'first_name' => 'alpha',
            'last_name' => 'alpha',
            'timezone' => 'string|max:50',
            'phone' => 'regex:/(^[+][0-9]+$)+/',
            'dob' => 'date|date_format:Y-m-d'
        ];
    }
}