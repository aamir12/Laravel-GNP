<?php

namespace App\Classes;

use App\Classes\BasePeriod;
use App\Classes\KpiAggregationMethod;
use App\Models\Score;
use App\Models\ScoreArchive;
use Illuminate\Support\Carbon;

class ScoreCalculator
{
    public static function setAggregatedScore($newScoreData)
    {
        $period = BasePeriod::getBasePeriodForTimestamp($newScoreData['timestamp']);
        $score = Score::where('user_id', $newScoreData['user_id'])
            ->whereBetween('timestamp', [$period['start'], $period['end']])
            ->first();

        if ($score) {
            $oldTimestamp = Carbon::parse($score->timestamp);
            $aggregationMethod = config('kpi.aggregation_method');

            if (
                $aggregationMethod === KpiAggregationMethod::LAST_VALUE &&
                $oldTimestamp->lt(Carbon::parse($newScoreData['timestamp']))
            ) {
                $score->value = $newScoreData['value'];
            } else if ($aggregationMethod === KpiAggregationMethod::WEIGHTED_AVERAGE) {
                $weightedValuesTotal = $score->value * $score->weight + $newScoreData['value'] * $newScoreData['weight'];
                $weightSum = $score->weight + $newScoreData['weight'];
                $score->value = $weightedValuesTotal / $weightSum;
                $score->weight = 1;
            } else if ($aggregationMethod === KpiAggregationMethod::SUM) {
                $score->value += $newScoreData['value'];
            } else {
                $archivedScores = ScoreArchive::where('user_id', $newScoreData['user_id'])
                    ->whereBetween('timestamp', [$period['start'], $period['end']])
                    ->first();
                $score->value = self::calcMode($archivedScores);
            }
            $score->timestamp = $newScoreData['timestamp'];
            $score->save();
        } else {
            Score::create($newScoreData);
        }
    }

    /**
     * Calculates the score value for a collection of KPI data points. The final score is
     * dependent on the KPI aggregation method environment setting.
     *
     * @param  \Illuminate\Support\Collection  $kpiData  A collection of Score model objects.
     * @return float
     */
    public static function calcScoreValue($kpiData)
    {
        if ($kpiData->isEmpty()) {
            return 0;
        }

        $aggregationMethod = config('kpi.aggregation_method');
        if ($aggregationMethod === KpiAggregationMethod::LAST_VALUE) {
            return $kpiData->last()->value;
        }
        if ($aggregationMethod === KpiAggregationMethod::WEIGHTED_AVERAGE) {
            return self::calcWeightedAvg($kpiData);
        }
        if ($aggregationMethod === KpiAggregationMethod::SUM) {
            return $kpiData->sum('value');
        }
        return self::calcMode($kpiData);
    }

    /**
     * Aggregates KPI data using weighted average method.
     *
     * @param  \Illuminate\Support\Collection  $kpiData
     * @return float
     */
    public static function calcWeightedAvg($kpiData)
    {
        $weightsTotal = $kpiData->sum('weight');

        if ($weightsTotal == 0) {
            return 0;
        }

        $weightedValuesTotal = $kpiData->map(function ($kpi) { return $kpi->value * $kpi->weight; })->sum();
        return $weightedValuesTotal / $weightsTotal;
    }

    /**
     * Aggregates KPI data using mode method.
     *
     * @param  \Illuminate\Support\Collection  $kpiData
     * @return float
     */
    public static function calcMode($kpiData)
    {
        // If data type is bool then this is a slight optimisation.
        if (config('kpi.data_type') === 'bool') {
            return round($kpiData->sum('value') / $kpiData->count());
        }
        return $kpiData->mode('value')[0];
    }

    public static function formatScoreValue($value) {
        return number_format((float) $value, 2, '.', '');
    }
}