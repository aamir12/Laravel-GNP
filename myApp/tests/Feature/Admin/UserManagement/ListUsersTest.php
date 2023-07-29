<?php

namespace Tests\Feature\Admin\UserManagement;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ListUsersTest extends TestCase
{
    private $url = '/api/admin/users/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListUsers()
    {
        User::factory()->count(5)->create();

        $response = $this->getJson($this->url);
        $response->assertOk();

        // Default super admin + admin user created in setUp + 5 users created above.
        $response->assertJsonCount(7, 'data');
    }

    /**
     * @dataProvider validUserFiltersProvider
     */
    public function testFilterUsersSucceedsWithValidData($filters)
    {
        User::factory()->count(5)->create();
        User::factory()->create($filters);

        $params = Arr::query($filters);
        $response = $this->getJson($this->url . '?' . $params);

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function validUserFiltersProvider()
    {
        return [
            'Email' => [ ['email' => 'test@test.com'] ],
            'First name' => [ ['first_name' => 'First'] ],
            'Last name' => [ ['last_name' => 'Last'] ],
            'Username' => [ ['username' => 'TestUser'] ],
            'Phone' => [ ['phone' => '+447777777777'] ],
            'DoB' => [ ['dob' => '1999-01-01'] ],
            'Multiple fields' => [
                [
                    'email' => 'test@test.com',
                    'first_name' => 'First',
                    'last_name' => 'Last',
                    'username' => 'TestUser',
                    'phone' => '+447777777777',
                    'dob' => '1999-01-01',
                ]
            ],
        ];
    }

    public function testUsersCanBeFilteredByGroup()
    {
        User::factory()->count(5)->create();
        $groupId = Group::factory()->hasUsers(2)->create()->id;

        $response = $this->getJson($this->url . '?group_id=' . $groupId);

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function testUsersCanBeFilteredByRole()
    {
        User::factory()->count(5)->create();

        // Super admin role has id of 3
        $response = $this->getJson($this->url . '?role=sa');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }
}
