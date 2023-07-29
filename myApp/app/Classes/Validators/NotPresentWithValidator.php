<?php

namespace App\Classes\Validators;

class NotPresentWithValidator 
{
    public function validateNotPresentWith($attribute, $value, $parameters, $validator) {

        // Add parameters for custom validation message.
        $validator->addReplacer('not_present_with', function($message, $attribute, $rule, $parameters) {
            return str_replace(':values', implode(' / ', $parameters), $message);
        });

        // Validate
        foreach ($parameters as $parameter) {
            if (array_get($validator->getData(), $parameter) !== null) {
                return false;
            }
        }
        return true;
    }
}
