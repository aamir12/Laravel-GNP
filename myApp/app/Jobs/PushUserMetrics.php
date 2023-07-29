<?php

namespace App\Jobs;

use App\Classes\BasePeriod;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PushUserMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Http::pool(function (Pool $pool) {
            $users = User::nonAdmins()->active()->get();
            foreach ($users as $user) {
                $scores = BasePeriod::getScoresForLastNBasePeriods(2, $user->id);
                $pool->kumulos()->put('/users/' . $user->id . '/attributes', [
                    'score' => $scores[1]['score_value'],
                    'previous_score' => $scores[0]['score_value'],
                    // 'group' => '',
                    'entered_competition' => $user->runningLotteries()->count() > 0 ? 1 : 0,
                ]);
            }
        });
    }
}
