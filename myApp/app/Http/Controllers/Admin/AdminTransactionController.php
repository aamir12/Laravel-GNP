<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\BulkUpdateTransactionsRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Models\Transaction;

/**
 * @group Admin API - Transactions
 *
 * Admin APIs for managing transactions.
 *
 * Transactions hold information relating to the transaction of user with amount, currency and reference.
 */
class AdminTransactionController extends Controller
{
    /**
     * List Transaction
     *
     * Lists all transactions along with some user info.
     *
     * @responseFile 200 resources/responses/Admin/Transaction/list.json
     */
    public function list()
    {
        $transactions = Transaction::with(['user' => function($query) {
            $query->without('roles', 'groups')->select('id', 'name', 'paypal_email');
        }])->get();

        return response()->success(__('transaction')['list_success'], $transactions);
    }

    /**
     * Update Transaction
     *
     * Updates the specified transaction with the values provided.
     *
     * @bodyParam id int required The ID of the transaction being updated. Example: 1
     * @bodyParam status string The status of the transaction. Must be one of: failed/cancelled/pending/complete. Example: pending
     * @bodyParam reference string A reference string for the transaction. Example: ABC-123
     *
     * @responseFile 200 resources/responses/Admin/Transaction/update.json
     */
    public function update(UpdateTransactionRequest $req)
    {
        $transaction = Transaction::find($req->id);
        $transaction->update($req->validated());

        return $transaction->wasChanged()
            ? response()->success(__('transaction')['update_success'], $transaction)
            : response()->success(__('nothing_updated'), $transaction);
    }

    /**
     * Bulk Update Transactions
     *
     * Updates the transactions specified in an array with the values provided.
     *
     * @bodyParam transactions[0][id] int required The ID of the transaction being updated. Example: 1
     * @bodyParam transactions[0][status] string The status of the transaction. Must be one of: failed/cancelled/pending/complete. Example: pending
     * @bodyParam transactions[0][reference] string A reference string for the transaction. Example: ABC-123
     *
     * @responseFile 200 resources/responses/Admin/Transaction/bulk-update.json
     * @responseFile 422 resources/responses/Admin/Transaction/bulk-update-422.json
     */
    public function bulkUpdate(BulkUpdateTransactionsRequest $req)
    {
        $transactions = [];

        foreach ($req->validated()['transactions'] as $data) {
            $transaction = Transaction::find($data['id']);
            $transactions[] = $transaction->update($data);
        }

        return response()->success(
            __('transaction')['bulk_update_success'],
            $transactions
        );
    }
}