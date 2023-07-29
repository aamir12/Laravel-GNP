<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\HasPasswordField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    use HasPasswordField;

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
        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('is_activated', 1);
                }),
            ],
            'username' => 'filled|alpha_dash|unique:users',
            'paypal_email' => 'email',
            'password' => $this->passwordRules(),
            'dob' => 'date|date_format:Y-m-d|before:' . now()->format('Y-m-d'),
            'metadata' => 'json',
        ];

        // If usernames are not randomly generated, they will be automatically
        // built from first name and last name.
        if (! config('app.generate_usernames')) {
            $rules['first_name'] = 'required|alpha';
            $rules['last_name'] = 'required|alpha';
        }

        if (config('app.open_registration')) {
            return $rules;
        }

        if (config('auth.external_id_account_activation')) {
            $rules['external_id'] = 'required|exists:users';
        } else {
            $rules['activation_code'] = 'required|exists:users';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.regex' => $this->passwordErrors($this),
            'activation_code.required' => __('auth')['invite_required'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'email' => 'Email',
            'external_id' => config('auth.external_id_label') ?: 'External ID'
        ];
    }
}
