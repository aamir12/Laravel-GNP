<?php

namespace App\Http\Requests\Deliverable;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliverableRequest extends FormRequest
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
            'id' => 'required|exists:deliverables',
            'is_shipped' => 'boolean',
            'shipping_name' => 'string',
            'shipping_number' => 'string',
            'shipping_email' => 'email',
            'shipping_addressline1' => 'string',
            'shipping_addressline2' => 'string',
            'shipping_addressline3' => 'string',
            'shipping_postcode' => 'string',
            'shipping_county' => 'string',
            'shipping_country' => 'string',
            'shipping_comment' => 'string',
            'tracking_ref' => 'string',
        ];
    }
}
