<?php

namespace App\Http\Requests\EmailConfig;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailConfigRequest extends FormRequest
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
            'id' => 'required_without:email_type|
                not_present_with:email_type|
                exists:email_configs',
            'email_type' => 'required_without:id|
                not_present_with:id|
                exists:email_configs,email_type',
            'is_enabled' => 'boolean',
            'subject' => 'string',
            'body' => 'string',
            'resend_interval' => 'integer',
        ];
    }
}
