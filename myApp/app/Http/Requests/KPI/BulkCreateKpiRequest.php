<?php

namespace App\Http\Requests\KPI;

use App\Classes\Validators\KpiValidator;
use Illuminate\Foundation\Http\FormRequest;

class BulkCreateKpiRequest extends FormRequest
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
        $rules = [
            'kpi' => 'required|Array',
            'kpi.*.user_id' => 'required_without_all:kpi.*.email,kpi.*.external_id|
                                not_present_with:kpi.*.email,kpi.*.external_id|
                                exists:users,id',
            'kpi.*.email' => 'required_without_all:kpi.*.user_id,kpi.*.external_id|
                              not_present_with:kpi.*.user_id,kpi.*.external_id',
            'kpi.*.external_id' => 'required_without_all:kpi.*.email,kpi.*.user_id|
                                    not_present_with:kpi.*.email,kpi.*.user_id|
                                    max:255',
            'kpi.*.value' => KpiValidator::kpiValueValidationRules(),
            'kpi.*.weight' => 'numeric',
            'kpi.*.timestamp' => 'date|date_multi_format:Y-m-d H:i,Y-m-d H:i:s.v',
            'kpi.*.metadata' => 'json',
        ];

        if (! config('kpi.auto_create_users_on_kpi_submit')) {
            $rules['kpi.*.email'] .= '|exists:users,email';
            $rules['kpi.*.external_id'] .= '|exists:users,external_id';
        }

        return $rules;
    }

}
