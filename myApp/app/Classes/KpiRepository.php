<?php

namespace App\Classes;

use App\Classes\ScoreCalculator;
use App\Models\Meta;
use App\Models\Score;
use App\Models\ScoreArchive;
use App\Models\User;
use App\Services\UserService;

class KpiRepository
{
    public static function addKPI($kpiData)
    {
        if (config('kpi.ignore_duplicates')) {
            $kpiData = array_unique($kpiData, SORT_REGULAR);
        }

        $res = [];

        if (config('kpi.auto_create_users_on_kpi_submit')) {
            foreach ($kpiData as $data) {
                UserService::createIfMissing(
                    array_only($data, ['user_id', 'email', 'external_id' ])
                );
            }
        }

        foreach ($kpiData as $data) {
            $user = User::findBy($data['user_id'] ?? NULL, $data['email'] ?? NULL, $data['external_id'] ?? NULL);
            $result = self::addKPIForUser($data, $user);
            $res[] = $result->getAttributes();
        }
        return $res;
    }

    public static function addKPIForUser($kpi, $user)
    {
        $data = [
            'user_id' => $user->id,
            'value' => $kpi['value'],
            'weight' => $kpi['weight'] ?? 1,
            'timestamp' => $kpi['timestamp'] ?? now()->format('Y-m-d H:i:s.v'),
        ];

        if (config('kpi.destructive_update')) {
            $score = ScoreArchive::create($data);
            ScoreCalculator::setAggregatedScore($data);
            $result = ScoreArchive::find($score->id);
        } else {
            $score = Score::create($data);
            $result = Score::find($score->id);
        }

        $score->setMetadata(Meta::extractMetadata($kpi));
        return $result;
    }

    /**
     * Returns aggregated score for KPI within a given period for a given user along
     * with other information related to the score.
     *
     * @param  array  $period
     * @param  int  $userId
     * @return array
     */
    public static function getScoreForUser($period, $userId)
    {
        $kpiData = Score::where('user_id', $userId)
            ->whereBetween('timestamp', [$period['start'], $period['end']])
            ->orderBy('timestamp')
            ->get();

        return [
            'score_value' => ScoreCalculator::formatScoreValue(ScoreCalculator::calcScoreValue($kpiData)),
            'score_unit' => config('kpi.score_unit') ?? NULL,
            'score_true_text' => config('kpi.true_text') ?? NULL,
            'score_false_text' => config('kpi.false_text') ?? NULL,
            'data_type' => config('kpi.data_type'),
            'period_start' => $period['start']->format('Y-m-d H:i:s'),
            'period_end' => $period['end']->format('Y-m-d H:i:s'),
        ];
    }

    public static function listKpiDataForUser($userId, $paginated = false)
    {
        $query = Score::orderBy('timestamp', 'desc');

        if (isset($userId)) {
            $query->where('user_id', $userId);
        }
        return $paginated ? $query->paginate() : $query->get();
    }
}
