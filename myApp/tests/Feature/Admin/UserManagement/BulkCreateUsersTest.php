<?php

namespace Tests\Feature\Admin\UserManagement;

use App\Mail\InvitedToEarnie;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BulkCreateUsersTest extends TestCase
{
    private $url = '/api/admin/users/bulk-create';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidUsersArrayProvider
     */
    public function testBulkCreateExpectsUsersArray(array $requestData)
    {
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
    }

    public function invalidUsersArrayProvider(): array
    {
        return [
            'No users data' => [[]],
            'Non-array users value' => [['users' => 1]],
        ];
    }

    /**
     * @dataProvider invalidUserDataProvider
     */
    public function testBulkCreateFailsWithInvalidData(array $overrides)
    {
        $requestData = ['users' => [$this->makeUserRequestData($overrides)]];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
    }

    /**
     * @dataProvider invalidUserDataProvider
     */
    public function testInvitationEmailsAreNotSentWhenUserCreationFails(array $overrides)
    {
        Mail::fake();
        $requestData = ['users' => [$this->makeUserRequestData($overrides)]];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
        Mail::assertNothingQueued(InvitedToEarnie::class);
    }

    public function invalidUserDataProvider(): array
    {
        return [
            'Empty email and empty external_id' => [['email' => '', 'external_id' => '']],
            'Invalid email' => [['email' => 'not-a-valid-email']],
            'Numeric first name' => [['first_name' => '123456']],
            'Numeric last name' => [['last_name' => '123456']],
            'Non-numeric phone' => [['phone' => 'abcdefg']],
            'Invalid dob' => [['dob' => 'not a valid dob']],
            'Invalid groups array' => [['groups' => 'not-an-array']],
            'Negative group id' => [['groups' => [-1]]],
            'Non-numeric group id' => [['groups' => ['abcdefg']]],
            'Nonexistant group id' => [['groups' => [1000000]]],
            'Non json metadata' => [['metadata' => 'Not valid json']],
        ];
    }

    public function testBulkCreateRequiresUniqueEmails()
    {
        // Duplicate emails in request
        $requestData = [
            'users' => [
                $this->makeUserRequestData(),
                $this->makeUserRequestData(),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);

        // Email in request already exists in DB.
        $user = User::factory()->create();
        $requestData['users'][1]['email'] = $user->email;
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 3);
    }

    public function testBulkCreateRequiresUniqueExternalId()
    {
        // Duplicate external ids in request
        $requestData = [
            'users' => [
                $this->makeUserRequestData(['external_id' => 'ABC-123']),
                $this->makeUserRequestData(['external_id' => 'ABC-123']),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);

        // Email in request already exists in DB.
        $user = User::factory()->create(['external_id' => 'DEF-456']);
        $requestData['users'][1]['external_id'] = $user->external_id;
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 3);
    }

    public function testBulkCreateFailsIfASingleEntryIsInvalid()
    {
        $requestData = [
            'users' => [
                $this->makeUserRequestData(['email' => 'test1@example.com']),
                $this->makeUserRequestData(['email' => 'test2@example.com']),
                $this->makeUserRequestData(['email' => 'invalid']),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
    }

    public function testBulkCreateFailsWithDeletedGroupId()
    {
        $group = Group::factory()->default()->create();
        $group->delete();

        $requestData = [
            'users' => [
                $this->makeUserRequestData(['groups' => [$group->id]]),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
    }

    public function testBulkCreateFailsWithParentGroupId()
    {
        $parentGroup = Group::factory()->default()->create();
        Group::factory()->create(['parent_id' => $parentGroup->id]);

        $requestData = [
            'users' => [
                $this->makeUserRequestData(['groups' => [$parentGroup->id]]),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('users', 2);
    }

    public function testBulkCreateSucceedsWithEmails()
    {
        $requestData = [
            'users' => [
                $this->makeUserRequestData(['email' => 'test1@example.com']),
                $this->makeUserRequestData(['email' => 'test2@example.com']),
                $this->makeUserRequestData(['email' => 'test3@example.com']),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);

        $response->assertOk();
        $this->assertDatabaseCount('users', 5);
    }

    public function testInvitationEmailsAreSentToCreatedUsers()
    {
        Mail::fake();
        $requestData = [
            'users' => [
                $this->makeUserRequestData(['email' => 'test1@example.com']),
                $this->makeUserRequestData(['email' => 'test2@example.com']),
                $this->makeUserRequestData(['email' => 'test3@example.com']),
            ]
        ];
        $this->postJson($this->url, $requestData);
        Mail::assertQueued(InvitedToEarnie::class, 3);
    }

    public function testBulkCreateSucceedsWithExternalIds()
    {
        $requestData = [
            'users' => [
                $this->makeUserRequestData(['email' => '', 'external_id' => 'ABC-123']),
                $this->makeUserRequestData(['email' => '', 'external_id' => 'DEF-456']),
                $this->makeUserRequestData(['email' => '', 'external_id' => 'GHI-789']),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
    }


    public function testBulkCreateSucceedsWithEmailOrExternalId()
    {
        $requestData = [
            'users' => [
                $this->makeUserRequestData(['email' => '', 'external_id' => 'ABC-123']),
                $this->makeUserRequestData(['email' => 'test@example.com']),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
    }

    public function testBulkCreateSucceedsWithEmailAndExternalId()
    {
        $requestData = [
            'users' => [
                $this->makeUserRequestData(['email' => 'test1@example.com', 'external_id' => 'ABC-123']),
                $this->makeUserRequestData(['email' => 'test2@example.com', 'external_id' => 'DEF-456']),
            ]
        ];
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
    }

    public function testNewUsersAreAddedToTheDefaultGroup()
    {
        $group = Group::factory()->default()->create();

        $requestData = [
            'users' => [$this->makeUserRequestData()]
        ];

        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();

        $this->assertDatabaseHas('group_user', [
            'group_id' => $group->id,
            'user_id' => $response->json('data.0.id'),
        ]);
    }

    public function testNewUsersAreNotAddedToTheDefaultGroupIfOtherGroupsAreSpecified()
    {
        $defaultGroup = Group::factory()->default()->create();
        $group = Group::factory()->create();

        $requestData = [
            'users' => [
                $this->makeUserRequestData(['groups' => [$group->id]]),
            ]
        ];

        dump($requestData);

        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();

        $this->assertDatabaseMissing('group_user', [
            'group_id' => $defaultGroup->id,
            'user_id' => $response->json('data.0.id'),
        ]);

        $this->assertDatabaseHas('group_user', [
            'group_id' => $group->id,
            'user_id' => $response->json('data.0.id'),
        ]);
    }

    private function makeUserRequestData(array $overrides = []): array
    {
        $defaults = [
            'email' => 'test@example.com',
        ];
        return array_merge($defaults, $overrides);
    }
}
