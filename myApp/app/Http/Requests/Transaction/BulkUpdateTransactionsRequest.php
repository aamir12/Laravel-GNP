<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateTransactionsRequest extends FormRequest
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
            'transactions' => 'required|array',
            'transactions.*.id' => 'required|exists:transactions',
            'transactions.*.status' => 'in:failed,cancelled,pending,complete',
            'transactions.*.reference' => 'string',
        ];
    }

}
