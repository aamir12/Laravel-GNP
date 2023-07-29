<?php

namespace App\Actions;

use App\Models\AchievementWinner;
use App\Models\Deliverable;
use App\Models\User;
use Exception;

class ClaimAchievementAction
{
    public function execute(User $user, AchievementWinner $achievementWinner)
    {
        if ($achievementWinner->user_id !== $user->id) {
            throw new Exception(__('achievement')['not_achievement_winner']);
        }
        if ($achievementWinner->is_claimed) {
            throw new Exception(__('achievement')['already_claimed']);
        }

        if (! $achievementWinner->deliverable_id) {
            $deliverable = Deliverable::create(['is_shipped' => 0]);
            $achievementWinner->deliverable_id = $deliverable->id;
        }

        $achievementWinner->is_claimed = 1;
        $achievementWinner->save();
    }
}
