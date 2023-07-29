<?php

namespace App\Classes;

use App\Models\Transaction;

class TransactionManager
{
    public static function deposit($userId, $amount, $currency, $reference)
    {
        $data = [
            'user_id' => $userId,
            'amount' => abs($amount),
            'currency' => $currency,
            'reference' => $reference,
            'status' => 'pending',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];
        Transaction::create($data);
    }

    public static function withdraw($userId, $amount, $currency, $reference)
    {
        $data = [
            'user_id' => $userId,
            'amount' => (0 - abs($amount)),
            'currency' => $currency,
            'reference' => $reference,
            'status' => 'pending',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];
        Transaction::create($data);
    }
}
