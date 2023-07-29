<?php

namespace App\Classes\Validators;

class DateMultiFormatValidator 
{
    public function validateDateFormats($attribute, $value, $parameters, $validator) {
        foreach ($parameters as $format) {
            $parsed = date_parse_from_format($format, $value);

            if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) {
                return true;
            }
        }

        $validator->addReplacer('date_multi_format', function($message, $attribute, $rule, $parameters) {
            return str_replace(':formats', implode(' | ', $parameters), $message);
        });

        return false;
    }
}
