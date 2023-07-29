<?php

namespace App\Classes\Validators;

class KpiValidator
{
    public static function kpiValueValidationRules()
    {
        $rules = 'required';
        $dataType = config('kpi.data_type');

        if ($dataType === 'integer') {
            $rules .= '|numeric|integer';
        } else if ($dataType === 'bool') {
            $rules .= '|boolean';
        } else {
            $rules .= '|numeric';
        }
        return $rules;
    }
}