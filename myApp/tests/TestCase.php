<?php

namespace Tests;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
        Storage::fake('testing');
    }

    protected function createUserAndLogin(bool $isAdmin = false)
    {
        $user = User::factory()->create();
        $role = Role::firstWhere('name', $isAdmin ? 'admin' : 'user');
        RoleUser::create(['user_id' => $user->id, 'role_id' => $role->id]);
        Passport::actingAs($user);
        return $user;
    }

    public function invalidIdProvider(): array
    {
        return [
            'Empty id' => [ '' ],
            'Negative id' => [ -1 ],
            'Non-numeric id' => [ 'A' ],
            'Nonexistant id' => [ 10000000 ],
        ];
    }
}
