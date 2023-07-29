<?php

namespace App\Actions;

use App\Classes\TransactionManager;
use App\Models\Prize;
use App\Models\User;
use Exception;

class ClaimPrizeAction
{
    /**
     * Handle a user claiming a prize.
     *
     * @param User $user User who is claiming the prize.
     * @param Prize $prize The prize being claimed.
     * @return void
     * @throws Exception if operation failed.
     */
    public function execute(User $user, Prize $prize)
    {
        $winner = $prize->winners()->firstWhere('user_id', $user->id);

        if (! $winner) {
            throw new Exception(__('prize')['not_prize_winner']);
        }

        if ($winner->is_claimed) {
            throw new Exception(__('prize')['already_claimed']);
        }

        $winner->is_claimed = true;
        $winner->save();

        $stock = $prize->stock;
        if ($stock->type === 'cash') {
            TransactionManager::deposit(
                $user->id,
                $stock->amount,
                $stock->currency,
                'Competition Prize'
            );
        }
    }
}