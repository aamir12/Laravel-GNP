<?php

namespace App\Classes;

use App\Classes\KpiRepository;
use App\Classes\BasePeriod;
use App\Models\Competition;
use App\Models\Deliverable;
use App\Models\Meta;
use App\Models\Winner;
use App\Services\StorageService;
use Carbon\Carbon;

class CompetitionManager
{
    public static function create($data)
    {
        $data['period'] = $data['period'] ?? config('kpi.base_period');

        $data = array_merge($data, StorageService::storeImage($data['image']));

        $competition = new Competition($data);
        $competition->end_date = $data['end_date'] ?? $competition->calcEndDate();
        $competition->save();

        if (isset($data['groups'])) {
            $competition->groups()->sync(array_unique($data['groups']));
        }

        $competition->setMetadata(Meta::extractMetadata($data));
        $competition->refresh();
        $competition->load(['groups', 'meta']);
        return $competition;
    }

    public static function competitionListEntrants($req)
    {
        $competition = Competition::with('entrants')->find($req->id);
        $prizeIds = $competition->prizes->modelKeys();
        foreach ($competition['entrants'] as $key => $value) {
            $winner = Winner::with('prize.stock')
                ->where('user_id', $value->id)
                ->whereIn('prize_id', $prizeIds)
                ->get();
            if (count($winner) > 0) {
                $value['has_won'] = true;
                $value['winner'] = $winner;
                $value['shipping'] = Deliverable::find($winner[0]->deliverable_id);
            } else {
                if (isset($req->winner) && $req->winner == 1) {
                    unset($competition['entrants'][$key]);
                } else {
                    $value['has_won'] = false;
                }
            }
            $score = KpiRepository::getScoreForUser(
                [
                    'start' => Carbon::parse($competition->start_date),
                    'end' => Carbon::parse($competition->end_date)
                ],
                $value->id
            );
            $value['final_score'] = $score['score_value'];
        }
        return $competition;
    }

    public static function getIntervalFromKPIBasePeriod()
    {
        return config('kpi.base_period') === 'daily' ? 2
               : (config('kpi.base_period') === 'weekly' ? 14
               : 60);
    }

    public static function checkEntrantAndHasWon($data, $userId)
    {
        foreach ($data as $key => $value) {
            if (isset($value['prizes'][0])) {
                $winnerCount = Winner::where([
                    'user_id' => $userId,
                    'prize_id' => (int)$value['prizes'][0]['id']
                ])->count();

                if ($winnerCount > 0) {
                    $data[$key]['has_won'] = true;
                }
            } else {
                $data[$key]['has_won'] = false;
            }
        }
        return $data;
    }

    public static function getLotteriesForUserDashboard($user)
    {
        $nextEndingLottery = $user->nextEndingLottery();
        $unrevealedLotteries = $user->unrevealedLotteries();

        return [
            'kpi_base_period' => config('kpi.base_period'),
            'kpi_data_type' => config('kpi.data_type'),
            'kpi_aggregation_method' => config('kpi.aggregation_method'),
            'upcoming_lottery' => $nextEndingLottery,
            'unrevealed_lotteries' => $unrevealedLotteries,
            'score' => BasePeriod::getScoresForLastNBasePeriods(1, $user->id),
            'daily_score' => KpiRepository::getScoreForUser(
                [
                    'start' => today(),
                    'end' => now(),
                ],
                $user->id
            ),
        ];
    }
}
