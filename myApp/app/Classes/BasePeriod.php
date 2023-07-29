<?php

namespace App\Classes;

use App\Classes\KpiRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;

class BasePeriod
{
    /**
     * Returns an array of date ranges with `start` and `end` keys. The date ranges are
     * determined by the KPI_BASE_PERIOD environment setting.
     *
     * @param  int  $num
     * @return array
     */
    public static function getLastNBasePeriods($num)
    {
        $dates = [];
        switch (config('kpi.base_period')) {
            case 'daily':
                for ($i = $num - 1; $i >= 0; $i--) {
                    if ($i == 0) {
                        $dates[] = [
                            'start' => now()->startOfDay(),
                            'end' => now()
                        ];
                        break;
                    }
                    $dates[] = [
                        'start' => now()->subDay($i)->startOfDay(),
                        'end' => now()->subDay($i - 1)->startOfDay()
                    ];
                }
                break;
            case 'weekly':
                for ($i = $num - 1; $i >= 0; $i--) {
                    if ($i == 0) {
                        $dates[] = [
                            'start' => now()->startOfWeek(),
                            'end' => now()
                        ];
                        break;
                    }
                    $dates[] = [
                        'start' => now()->subWeek($i)->startOfWeek(),
                        'end' => now()->subWeek($i - 1)->startOfWeek()
                    ];
                }
                break;
            case 'monthly':
                for ($i = $num - 1; $i >= 0; $i--) {
                    if ($i == 0) {
                        $dates[] = [
                            'start' => now()->firstOfMonth(),
                            'end' => now()
                        ];
                        break;
                    }
                    $dates[] = [
                        'start' => now()->subMonth($i)->firstOfMonth(),
                        'end' => now()->subMonth($i - 1)->startOfMonth()
                    ];
                }
                break;
        }
        return $dates;
    }

    /**
     * Returns the scores for a given user for the last N base periods.
     *
     * @param  int  $n How many base periods to include.
     * @param  int  @userId
     * @return array
     */
    public static function getScoresForLastNBasePeriods($n, $userId)
    {
        $basePeriods = self::getLastNBasePeriods($n);
        foreach ($basePeriods as $period) {
            $result[] = KpiRepository::getScoreForUser($period, $userId);
        }
        return $result;
    }

    /**
     * Returns the base period within which a given timestamp resides.
     *
     * @param  Carbon  $timestamp
     * @return array
     */
    public static function getBasePeriodForTimestamp($timestamp)
    {
        $date = CarbonImmutable::parse($timestamp);

        switch (config('kpi.base_period')) {
            case 'daily':
                return [
                    'start' => $date->startOfDay(),
                    'end' => now()->min($date->addDay()->startOfDay()),
                ];
            case 'weekly':
                return [
                    'start' => $date->startOfWeek(),
                    'end' => now()->min($date->addWeek()->startOfWeek()),
                ];
            case 'monthly':
                return [
                    'start' => $date->startOfMonth(),
                    'end' => now()->min($date->addMonth()->startOfMonth()),
                ];
        }
    }
}