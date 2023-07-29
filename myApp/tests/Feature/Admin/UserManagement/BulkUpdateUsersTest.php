<?php

namespace Tests\Feature\Admin\UserManagement;

use App\Models\User;
use Tests\TestCase;

class BulkUpdateUsersTest extends TestCase
{
    private $url = '/api/admin/users/bulk-update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidUserDataProvider
     */
    public function testBulkUpdateFailsWithInvalidData(array $overrides)
    {
        $user = User::factory()->create();
        $overrides['id'] = $user->id;
        $requestData = ['users' => [ $this->makeUserRequestData($overrides) ] ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $user->email]);
    }

    public function invalidUserDataProvider(): array
    {
        return [
            'Empty email' => [ ['email' => ''] ],
            'Invalid email' => [ ['email' => 'not-a-valid-email'] ],
            'Numeric first name' => [ ['first_name' => '123456'] ],
            'Numeric last name' => [ ['last_name' => '123456'] ],
            'Non-numeric phone' => [ ['phone' => 'abcdefg'] ],
            'Invalid dob' => [ ['dob' => 'not a valid dob'] ],
            'Invalid groups array' => [ ['groups' => 'not-an-array'] ],
            'Negative group id' => [ ['groups' => [-1] ] ],
            'Non-numeric group id' => [ ['groups' => ['abcdefg'] ] ],
            'Nonexistant group id' => [ ['groups' => [1000000] ] ],
            'Non json metadata' => [ ['metadata' => 'Not valid json'] ],
        ];
    }

    public function testBulkUpdateRequiresDistinctUserIds()
    {
        $user = User::factory()->create();
        $requestData = [
            'users' => [
                ['id' => $user->id, 'first_name' => 'Neil'],
                ['id' => $user->id, 'last_name' => 'Peart'],
            ]
        ];
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    public function testBulkUpdateRequiresUniqueEmails()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Duplicate emails in request
        $requestData = [
            'users' => [
                ['id' => $user1->id, 'email' => 'test@test.com'],
                ['id' => $user2->id, 'email' => 'test@test.com'],
            ]
        ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('users', ['id' => $user1->id, 'email' => $user1->email]);
        $this->assertDatabaseHas('users', ['id' => $user2->id, 'email' => $user2->email]);

        // Email in request already exists in DB.
        $user3 = User::factory()->create();
        $requestData['users'][1]['email'] = $user3->email;
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('users', ['id' => $user2->id, 'email' => $user2->email]);
    }

    public function testBulkUpdateSucceedsWithValidData()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $requestData = [
            'users' => [
                ['id' => $user1->id, 'first_name' => 'Alex'],
                ['id' => $user2->id, 'last_name' => 'Lifeson'],
            ]
        ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $user1->id, 'first_name' => 'Alex']);
        $this->assertDatabaseHas('users', ['id' => $user2->id, 'last_name' => 'Lifeson']);
    }

    private function makeUserRequestData(array $overrides = []): array
    {
        $defaults = [
            'email' => 'test@test.com',
        ];
        return array_merge($defaults, $overrides);
    }
}
