<?php

namespace Tests\Feature\User\Dashboard;

use App\Classes\KpiAggregationMethod;
use App\Models\Competition;
use Carbon\Carbon;
use Database\Seeders\KpiSeeder;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    private $url = '/api/user/dashboard';

    public function testAuthenticationRequired()
    {
        $response = $this->getJson($this->url);
        $response->assertUnauthorized();
    }

    public function testUserAuthorisationRequired()
    {
        $this->createUserAndLogin(true);
        $response = $this->getJson($this->url);
        $response->assertForbidden();
    }

    /**
     * Tests that the score value returned by the dashboard endpoint is calculated correctly for all
     * KPI aggregation method and base period combinations.
     *
     * @dataProvider kpiConfigProvider
     */
    // public function testDashboardScoreValueIsCorrect($aggregationMethod, $basePeriod, $expectedScore)
    // {
    //     $user = $this->createUserAndLogin(false);
    //     (new KpiSeeder())->run($user);
    //     $this->setKpiConfig($aggregationMethod, $basePeriod);

    //     $response = $this->getJson($this->url);

    //     $response->assertOk();
    //     $response->assertJsonPath('data.score.0.score_value', $expectedScore);
    // }

    public function kpiConfigProvider()
    {
        return [
            'Daily + Weightedaverage' => [
                KpiAggregationMethod::WEIGHTED_AVERAGE,
                'daily',
                '14.29'
            ],
            'Daily + Sum' => [
                KpiAggregationMethod::SUM,
                'daily',
                '40.00'
            ],
            'Daily + Mode' => [
                KpiAggregationMethod::MODE,
                'daily',
                '10.00'
            ],
            'Daily + Last_value' => [
                KpiAggregationMethod::LAST_VALUE,
                'daily',
                '20.00'
            ],
            'Weekly + Weightedaverage' => [
                KpiAggregationMethod::WEIGHTED_AVERAGE,
                'weekly',
                '13.75'
            ],
            'Weekly + Sum' => [
                KpiAggregationMethod::SUM,
                'weekly',
                '90.00'
            ],
            'Weekly + Mode' => [
                KpiAggregationMethod::MODE,
                'weekly',
                '10.00'
            ],
            'Weekly + Last_value' => [
                KpiAggregationMethod::LAST_VALUE,
                'weekly',
                '20.00'
            ],
            'Monthly + Weightedaverage' => [
                KpiAggregationMethod::WEIGHTED_AVERAGE,
                'monthly',
                '13.33'
            ],
            'Monthly + Sum' => [
                KpiAggregationMethod::SUM,
                'monthly',
                '150.00'
            ],
            'Monthly + Mode' => [
                KpiAggregationMethod::MODE,
                'monthly',
                '10.00'
            ],
            'Monthly + Last_value' => [
                KpiAggregationMethod::LAST_VALUE,
                'monthly',
                '20.00'
            ]
        ];
    }

    public function seedLotteries($user, $isRevealed)
    {
        $startDate = Carbon::now()->subDays(2)->addHours(1);
        $start = [];
        $end = [];
        for ($i = 0; $i < 4; $i++) {
            $start[$i] = $startDate;
            $startDate = Carbon::parse($startDate)->add('1', 'day');
            $end[$i] = $startDate;
        }
        Competition::factory()->count(4)->create([
            'is_lottery' => 1,
            'type' => 'Rolling'
        ])->each(function ($competition, $index) use($start, $end, $user, $isRevealed) {
            $competition->start_date = $start[$index];
            $competition->end_date = $end[$index];
            $competition->save();
            $competition->entrants()->attach($user->id, ['competition_revealed' => $isRevealed]);
        });
    }

    public function setKpiConfig($aggregationMethod, $basePeriod)
    {
        config(['kpi.base_period' => $basePeriod]);
        config(['kpi.aggregation_method' => $aggregationMethod]);
    }
}