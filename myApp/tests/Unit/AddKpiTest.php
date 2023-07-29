<?php

namespace Tests\Unit;

use App\Classes\KpiRepository;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Score;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Http\Response;

class AddKpiTest extends TestCase
{
     /**
     * Create Kpi test.
     *
     * @dataProvider createBulkKpiProvider
     *
     * @return void
     */
    public function testBulkKpi($kpiData, $case, $status)
    {
        $user_id = $this->createUser();
        $request = $this->createRequest($kpiData, $case, $user_id);
        $result = KpiRepository::addKPI($request['kpi']);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function createBulkKpiProvider()
    {
        $this->createApplication();

        return [
            'No previous data' => [
                ['kpi' => [ Score::factory()->make()->toArray() ]],
                false,
                'statusCode' => Response::HTTP_OK
            ],
            'No destructive update' => [
                ['kpi' => [ Score::factory()->make()->toArray() ]],
                'no-destructive-update',
                'statusCode' => Response::HTTP_OK
            ],
            'Daily Sum as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'daily-sum',
                'statusCode' => Response::HTTP_OK
            ],
            'Daily Weightedaverage as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'daily-weightedaverage',
                'statusCode' => Response::HTTP_OK
            ],
            'Daily Mode as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'daily-mode',
                'statusCode' => Response::HTTP_OK
            ],
            'Daily Last value as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'daily-last_value',
                'statusCode' => Response::HTTP_OK
            ],
            'Weekly Sum as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'weekly-sum',
                'statusCode' => Response::HTTP_OK
            ],
            'Weekly Weightedaverage as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'weekly-weightedaverage',
                'statusCode' => Response::HTTP_OK
            ],
            'Weekly Mode as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'weekly-mode',
                'statusCode' => Response::HTTP_OK
            ],
            'Weekly Last value as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'weekly-last_value',
                'statusCode' => Response::HTTP_OK
            ],
            'Monthly Sum as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'monthly-sum',
                'statusCode' => Response::HTTP_OK
            ],
            'Monthly Weightedaverage as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'monthly-weightedaverage',
                'statusCode' => Response::HTTP_OK
            ],
            'Monthly Mode as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'monthly-mode',
                'statusCode' => Response::HTTP_OK
            ],
            'Monthly Last value as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'monthly-last_value',
                'statusCode' => Response::HTTP_OK
            ]
        ];
    }

     /**
     * Create User Kpi test.
     *
     * @dataProvider createUserKpiProvider
     *
     * @return void
     */
    public function testUserKpi($kpiData, $case, $status)
    {
        $user_id = $this->createUser('user');
        $request = $this->createRequest($kpiData, $case, $user_id);
        $result = KpiRepository::addKPI($request['kpi']);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function createUserKpiProvider()
    {
        $this->createApplication();

        return [
            'No previous data' => [
                ['kpi' => [ Score::factory()->make()->toArray() ]],
                false,
                'statusCode' => Response::HTTP_OK
            ],
            'No destructive update' => [
                ['kpi' => [ Score::factory()->make()->toArray() ]],
                'no-destructive-update',
                'statusCode' => Response::HTTP_OK
            ],
            'Daily Sum as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'daily-sum',
                'statusCode' => Response::HTTP_OK
            ],
            'Daily Weightedaverage as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'daily-weightedaverage',
                'statusCode' => Response::HTTP_OK
            ],
            'Daily Mode as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'daily-mode',
                'statusCode' => Response::HTTP_OK
            ],
            'Daily Last value as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'daily-last_value',
                'statusCode' => Response::HTTP_OK
            ],
            'Weekly Sum as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'weekly-sum',
                'statusCode' => Response::HTTP_OK
            ],
            'Weekly Weightedaverage as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'weekly-weightedaverage',
                'statusCode' => Response::HTTP_OK
            ],
            'Weekly Mode as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'weekly-mode',
                'statusCode' => Response::HTTP_OK
            ],
            'Weekly Last value as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'weekly-last_value',
                'statusCode' => Response::HTTP_OK
            ],
            'Monthly Sum as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'monthly-sum',
                'statusCode' => Response::HTTP_OK
            ],
            'Monthly Weightedaverage as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'monthly-weightedaverage',
                'statusCode' => Response::HTTP_OK
            ],
            'Monthly Mode as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'monthly-mode',
                'statusCode' => Response::HTTP_OK
            ],
            'Monthly Last value as Aggregation method' => [
                ['kpi' => [ Score::factory()->currentTimestamp()->make()->toArray() ]],
                'monthly-last_value',
                'statusCode' => Response::HTTP_OK
            ]
        ];
    }

    public static function createUser($type = 'admin')
    {
        $user = User::factory()->count(2)->create();
        $role = Role::firstWhere('name', 'admin');
        RoleUser::create(['user_id' => $user[0]->id, 'role_id' => $role->id]);
        $role = Role::firstWhere('name', 'user');
        RoleUser::create(['user_id' => $user[1]->id, 'role_id' => $role->id]);
        if ($type == 'user') {
            Passport::actingAs($user[1]);
        } else {
            Passport::actingAs($user[0]);
        }
        return $user[1]->id;
    }

    public static function createRequest($data, $case, $user_id)
    {
        $data['kpi'][0]['user_id'] = $user_id;
        if ($case !== 'no-destructive-update') {
            config(['kpi.destructive_update' => true]);
        } else {
            $caseType = explode('-', $case);
            if ($caseType[0] == 'daily') {
                config(['kpi.base_period' => 'daily']);
            } else if ($caseType[0] == 'weekly') {
                config(['kpi.base_period' => 'weekly']);
            } else if ($caseType[0] == 'monthly') {
                config(['kpi.base_period' => 'monthly']);
            }

            if ($caseType[1] == 'sum') {
                config(['kpi.aggregation_method' => 'sum']);
            } else if ($caseType[1] == 'weightedaverage') {
                config(['kpi.aggregation_method' => 'weightedaverage']);
            } else if ($caseType[1] == 'mode') {
                config(['kpi.aggregation_method' => 'mode']);
            } else if ($caseType[1] == 'last_value') {
                config(['kpi.aggregation_method' => 'last_value']);
            }
        }
        $request = new Request();
        return $request->replace($data);
    }
}
