<?php

namespace App\Http\Requests\Traits;

trait HasPasswordField
{
    protected function passwordRules()
    {
        $string = 'required';
        if (config('auth.password_validation.min')) {
            $string .= '|min:' . config('auth.password_validation.min');
        }
        if (config('auth.password_validation.max')) {
            $string .= '|max:' . config('auth.password_validation.max');
        }
        if (config('auth.password_validation.requires_lowercase_char')) {
            $string .= '|regex:/^.*(?=.*[a-z]).*$/';
        }
        if (config('auth.password_validation.requires_uppercase_char')) {
            $string .= '|regex:/^.*(?=.*[A-Z]).*$/';
        }
        if (config('auth.password_validation.requires_number')) {
            $string .= '|regex:/^.*(?=.*[0-9]).*$/';
        }
        if (config('auth.password_validation.requires_symbol')) {
            $string .= '|regex:/^.*(?=.*[\d\x])(?=.*[!$#%@&]).*$/';
        }
        return $string;
    }

    protected function passwordErrors($req)
    {
        if (
            config('auth.password_validation.requires_lowercase_char') &&
            !preg_match('/^.*(?=.*[a-z]).*$/', $req->password)
        ) {
            return __('auth')['password_requires_lowercase'];
        }

        if (
            config('auth.password_validation.requires_uppercase_char') &&
            !preg_match('/^.*(?=.*[A-Z]).*$/', $req->password)
        ) {
            return __('auth')['password_requires_uppercase'];
        }

        if (
            config('auth.password_validation.requires_number') &&
            !preg_match('/^.*(?=.*[0-9]).*$/', $req->password)
        ) {
            return __('auth')['password_requires_number'];
        }

        if (
            config('auth.password_validation.requires_symbol') &&
            !preg_match('/^.*(?=.*[\d\x])(?=.*[!$#%@&]).*$/', $req->password)
        ) {
            return __('auth')['password_requires_symbol'];
        }
    }
}