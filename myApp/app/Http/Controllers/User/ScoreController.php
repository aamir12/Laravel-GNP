<?php

namespace App\Http\Controllers\User;

use App\Classes\KpiAggregationMethod;
use App\Classes\BasePeriod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @group User API - Scores
 *
 * Scores are the end result of aggregating KPI data over a set time period. By
 * default, the time period used is the KPI base period.
 */
class ScoreController extends Controller
{
    /**
     * Get Score Summary
     *
     * Retrieves the currently authenticated user's scores for the last 5 base periods.
     *
     * @responseFile 200 resources/responses/User/Score/get-score.json
     * @responseFile 422 resources/responses/User/Score/get-score-422.json
    */
    public function getScore()
    {
        $dataType = config('kpi.data_type');
        $aggregation = config('kpi.aggregation_method');

        if ($dataType === 'bool' &&
            ($aggregation === KpiAggregationMethod::WEIGHTED_AVERAGE ||
            $aggregation === KpiAggregationMethod::SUM)) {
            return response()->error(__('score')['wrong_datatype_method_score_error']);
        }

        // Fetch scores for past 6 base periods but ditch the first one since this
        // route shouldn't include the current base period (e.g. the past 5 weeks,
        // not including this week).
        $scores = BasePeriod::getScoresForLastNBasePeriods(6, Auth::id());
        $scores = Arr::except($scores, 5);
        return response()->success(__('score')['user_score_success'], $scores);
    }
}
