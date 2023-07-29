<?php

namespace Tests\Feature\Admin\Competition;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CreateCompetitionTest extends TestCase
{
    private $url = '/api/admin/competitions/create';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidCompetitionDataProvider
     */
    public function testCreateCompetitionFailsWithInvalidData(array $overrides)
    {
        $requestData = $this->makeCompetitionRequestData($overrides);
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
     * @dataProvider validCompetitionDataProvider
     */
    public function testCreateCompetitionSucceedsWithValidData(array $overrides)
    {
        $requestData = $this->makeCompetitionRequestData($overrides);
        unset($requestData['period']);
        $requestData['end_date'] = Carbon::parse($requestData['start_date'])
                ->addDays(7)
                ->format('Y-m-d H:i');

        $group = Group::factory()->create();
        $requestData['groups'] = [$group->id];

        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
    }

    public function validCompetitionDataProvider(): array
    {
        return [
            'Valid name' => [ ['name' => 'Test Competition'] ],
            'Valid description' => [ ['description' => 'description'] ],
            'Valid image' => [ ['image' => UploadedFile::fake()->image('test.png')] ],
            'Valid type' => [ ['type' => 'Fixed'] ],
            'Valid score threshold' => [ ['score_threshold' => '100'] ],
            'Valid start date' => [ ['start_date' => Carbon::tomorrow()->format('Y-m-d H:i')] ],
            'Valid end date' => [ ['end_date' => Carbon::tomorrow()->format('Y-m-d H:i')] ],
            'Valid period' => [ ['period' => 'daily'] ],
            'Valid is lottery' => [ ['is_lottery' => '1'] ],
            'Valid space count' => [ ['space_count' => '0'] ],
            'Valid entry fee' => [ ['entry_fee' => '0'] ],
            'Valid auto enter user' => [ ['auto_enter_user' => '0'] ],
            'Valid threshold operator' => [ ['threshold_operator' => '>'] ],
            'valid-data-with-no-period' => [ ['threshold_operator' => '>'] ],
            'Valid data' => [ [] ],
            'valid groups' => [ ['groups' => ''] ],

        ];
    }

    private function makeCompetitionRequestData(array $overrides = []): array
    {
        $defaults = [
            'name' => 'Test Competition',
            'description' => 'A description for the competition.',
            'image' => UploadedFile::fake()->image('test.png'),
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
}
