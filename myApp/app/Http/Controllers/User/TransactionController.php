<?php

namespace App\Http\Controllers\User;

use App\Classes\TransactionManager;
use App\Http\Controllers\Controller;
use App\Mail\WithdrawToPaypalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * @group User API - Transactions
 *
 * Transactions provide a record of a user's cash flow. Each time an event
 * occurs that alters a user’s balance, a transaction is recorded.
 *
 * The system keeps a full audit history of all cash transactions. This is then
 * used to calculate a user’s current balance and also allows users to view
 * their full transaction history if needed.
 */
class TransactionController extends Controller
{
    /**
     * Withdraw
     *
     * @bodyParam amount float required Amount to withdraw. Example: 12.50
     */
    public function userBalanceWithdraw(Request $req)
    {
        $req->validate(['amount' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/']);

        $user = Auth::user();

        if ($req->amount > $user->balance()) {
            return response()->error(__('withdraw')['insufficent_balance']);
        }

        if (!isset($user->paypal_email)) {
            return response()->error(__('withdraw')['paypal_email_not_set']);
        }

        TransactionManager::withdraw(
            Auth::id(),
            $req['amount'], 'GBP', 'Withdrawn to Paypal'
        );

        Mail::to(config('mail.accounting_address'))
            ->send(new WithdrawToPaypalRequest($user, $req['amount']));

        return response()->success(__('withdraw')['success']);
    }

    /**
     * Get Balance
     *
     * @responseFile 200 resources/responses/User/Transaction/get-balance.json
     */
    public function getBalance()
    {
        $response = [
            'balance' => Auth::user()->balance(),
            'transactions' => Auth::user()->transactions,
        ];
        return response()->success(__('transaction')['balance_found'], $response);
    }
}
