<?php

namespace Tests\Feature\Admin\Competition;

use App\Models\Competition;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class UpdateCompetitionTest extends TestCase
{
    private $url = '/api/admin/competitions/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidCompetitionDataProvider
     */
    public function testUpdateCompetitionFailsWithInvalidData(array $overrides)
    {
        $requestData = $this->makeCompetitionRequestData($overrides);
        $oldCompetition = Competition::factory()->create([
            'end_date' => now()->addDays(2)->format('Y-m-d H:i')
        ]);
        $requestData['id'] = $oldCompetition->id;

        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
    }

    public function invalidCompetitionDataProvider(): array
    {
        return [
            'Empty name' => [ ['name' => ''] ],
            'Empty description' => [ ['description' => ''] ],
            'Empty image' => [ ['image' => -1] ],
            'Invalid type' => [ ['type' => 'Invalid'] ],
            'Empty score threshold' => [ ['score_threshold' => ''] ],
            'Invalid score threshold' => [ ['score_threshold' => 'Invalid'] ],
            'Empty start date' => [ ['start_date' => ''] ],
            'Invalid start date' => [ ['start_date' => '2021-10'] ],
            'Empty end date' => [ ['end_date' => ''] ],
            'Invalid end date' => [ ['end_date' => '2021-10'] ],
            'Invalid period' => [ ['period' => 'invalid'] ],
            'Invalid is lottery' => [ ['is_lottery' => 'not-a-bool'] ],
            'Invalid space count' => [ ['space_count' => 'not-an-integer'] ],
            'Invalid entry fee' => [ ['entry_fee' => 'not-an-integer'] ],
            'Invalid groups' => [ ['groups' => 'not-an-array'] ],
            'String array groups' => [ ['groups' => ['not-an-array']] ],
            'Invalid group id' => [ ['groups' => [0]] ],
            'Invalid auto enter user' => [ ['auto_enter_user' => 'non-bool'] ],
            'Invalid threshold operator' => [ ['threshold_operator' => ''] ],
        ];
    }

    /**
     * @dataProvider validUpdateCompetitionDataProvider
     */
    public function testUpdateCompetitionSucceedsWithValidData(array $overrides)
    {
        $requestData = $this->makeCompetitionRequestData($overrides);
        $oldCompetition = Competition::factory()->create(['end_date' => now()->addDays(2)->format('Y-m-d H:i')]);

        $group = Group::factory()->create();
        $requestData['groups'] = [$group->id];
        $requestData['id'] = $oldCompetition->id;
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
    }

    public function validUpdateCompetitionDataProvider(): array
    {
        return [
            'Valid name' => [ ['name' => 'Test1 adsdf Competition'] ],
            'Valid description' => [ ['description' => 'description1'] ],
            'Valid image' => [ ['image' => \Illuminate\Http\UploadedFile::fake()->image('test.png')] ],
            'Valid type' => [ ['type' => 'Fixed'] ],
            'Valid score threshold' => [ ['score_threshold' => '100'] ],
            'Valid start date' => [ ['start_date' => date('Y-m-d H:i', strtotime('+1 days'))] ],
            'Valid end date' => [ ['end_date' =>  date('Y-m-d H:i', strtotime('+2 days'))] ],
            // 'Valid period' => [ ['period' => 'daily'] ],
            'Valid is lottery' => [ ['is_lottery' => '1'] ],
            'Valid space count' => [ ['space_count' => '0'] ],
            'Valid entry fee' => [ ['entry_fee' => '0'] ],
            'Valid auto enter user' => [ ['auto_enter_user' => '0'] ],
            'Valid threshold operator' => [ ['threshold_operator' => '>'] ],
            'Valid data with no period' => [ ['threshold_operator' => '>'] ],
            'Valid data' => [ [] ],
            'valid groups' => [ ['groups' => ''] ],
            'Competition started only type update' => [ [] ],
            'Nothing update' => [ [] ],
        ];
    }

    /**
     * @dataProvider invalidPeriodUpdateCompetitionDataProvider
     */
    public function testUpdateCompetitionFailsWhenPeriodIs(array $overrides)
    {
        $requestData = $this->makeCompetitionRequestData($overrides);
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    public function invalidPeriodUpdateCompetitionDataProvider(): array
    {
        return [
            'Empty period' => [ ['period' => ''] ],
            'Invalid period' => [ ['period' => 'adfsadf'] ],
        ];
    }

    /**
     * @dataProvider validPeriodUpdateCompetitionDataProvider
     */
    public function testUpdateCompetitionSucceedsWhenPeriodIs(array $overrides)
    {
        $requestData = $this->makeCompetitionRequestData($overrides);

        $oldCompetition = Competition::factory()->create(['end_date' => now()->addDays(2)->format('Y-m-d H:i')]);
        $requestData['id'] = $oldCompetition->id;
        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
    }

    public function validPeriodUpdateCompetitionDataProvider(): array
    {
        return [
            'Valid period daily' => [ ['period' => 'daily'] ],
            'Valid period weekly' => [ ['period' => 'weekly'] ],
            'Valid period monthly' => [ ['period' => 'monthly'] ],
        ];
    }

    /**
     * @dataProvider validStatusUpdateCompetitionDataProvider
     */
    public function testUpdateCompetitionSucceedsWhenStatusIs(array $overrides)
    {
        $requestData = $this->makeCompetitionRequestData($overrides);

        $oldCompetition = Competition::factory()->create(['end_date' => now()->addDays(2)->format('Y-m-d H:i')]);

        if ($requestData['is_status'] == 'competition-started-no-update') {
            $startDate = now()->subDays(1)->format('Y-m-d H:i');
            $endDate = now()->addDays(3)->format('Y-m-d H:i');
            $oldCompetition = Competition::factory()->create(['start_date' => $startDate, 'end_date' => $endDate]);
            unset($requestData['type']);
        } else if ($requestData['is_status'] == 'competition-started-only-type-update') {
            $startDate = now()->subDays(1)->format('Y-m-d H:i');
            $oldCompetition = Competition::factory()->create(['start_date' => $startDate, 'end_date' => now()->addDays(2)->format('Y-m-d H:i')]);
            if ($oldCompetition->type == 'Fixed') {
                $requestData['type'] = 'Rolling';
            } else {
                $requestData['type'] = 'Fixed';
            }
        }
        $requestData['id'] = $oldCompetition->id;
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
    }

    public function validStatusUpdateCompetitionDataProvider(): array
    {
        return [
            'Competition started no update' => [ ['is_status' => 'Competition started no update'] ],
            'Competition started only type update' => [ ['is_status' => 'Competition started only type update'] ],
        ];
    }

    // public function testNewlyActivatedUserIsEnteredIntoLiveAutoEnterCompetitions()
    // {
    //     Artisan::call('migrate:fresh');
    //     Artisan::call('passport:install');

    //     $competition1 = Competition::factory()->autoEnter()->started()->create();
    //     $competition2 = Competition::factory()->autoEnter()->upcoming()->create();
    //     $competition3 = Competition::factory()->autoEnter()->ended()->create();
    //     $competition4 = Competition::factory()->started()->create();

    //     $userData = $this->makeRegisterRequestData();
    //     $this->postJson('/api/auth/register', $userData);

    //     $user = User::where(['email' => $userData['email']])->first();

    //     $this->assertDatabaseHas('competition_participants', [
    //         'competition_id' => $competition1->id,
    //         'user_id' => $user->id
    //     ]);

    //     $this->assertDatabaseMissing('competition_participants', [
    //         'competition_id' => $competition2->id,
    //         'user_id' => $user->id
    //     ]);

    //     $this->assertDatabaseMissing('competition_participants', [
    //         'competition_id' => $competition3->id,
    //         'user_id' => $user->id
    //     ]);

    //     $this->assertDatabaseMissing('competition_participants', [
    //         'competition_id' => $competition4->id,
    //         'user_id' => $user->id
    //     ]);

    //     Artisan::call('migrate:fresh');
    // }

    private function makeCompetitionRequestData(array $overrides = []): array
    {
        $defaults = [
            'name' => 'Test Competition',
            'description' => 'A description for the competition.',
            'image' => \Illuminate\Http\UploadedFile::fake()->image('test.png'),
            'type' => 'Fixed',
            'score_threshold' => '100',
            'threshold_operator' => '>',
            'start_date' => Carbon::tomorrow()->format('Y-m-d H:i'),
            'is_lottery' => 1,
            'space_count' => 0,
            'entry_fee' => 0,
            'auto_enter_user' => 0,
            'terms_url' => 'www.example.com',
            'status' => 'live',
            'competition_type' => 'test',

        ];
        return array_merge($defaults, $overrides);
    }

    private function makeRegisterRequestData(): array
    {
        return [
            'email' => 'test@test.com',
            'username' => 'Test123',
            'paypal_email' => 'test@test.com',
            'password' => 'Test@12345',
            'dob' => '2000-07-22'
        ];
    }
}
