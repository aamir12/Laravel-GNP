<?php

namespace App\Classes\Validators;
use App\Models\User;

class UserValidator
{
    public function validateUserExternalId($attribute, $value, $parameters, $validator) {
        $external_id = $value;
        $idAttr = str_replace('.external_id', '.id', $attribute);
        $id = request()->input($idAttr);
        if ($id) {
            $user = User::where('external_id', $external_id)->where('id', '!=', $id)->first();
            if ($user) {
                return false;
            }
        }
        return true;
    }
}