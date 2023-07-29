<?php

namespace App\Services;

use App\Models\Meta;
use App\Models\Role;
use App\Models\User;

class UserService
{
    /**
     * Create user and set roles, groups and metadata.
     */
    public static function createUser(array $data): User
    {
        $data['activation_code'] = self::generateActivationCode();
        $data['password'] = bcrypt(str_random());
        $user = User::create($data);

        if (isset($data['groups'])) {
            $user->groups()->sync($data['groups']);
        }

        $user->roles()->sync([Role::firstWhere('name', 'user')->id]);
        $user->setMetadata(Meta::extractMetadata($data));

        return $user;
    }

    /**
     * Bulk create users and set roles, groups and metadata.
     */
    public static function bulkCreateUsers(array $data): array
    {
        $users = [];
        foreach ($data['users'] as $userData) {
            $users[] = self::createUser($userData);
        }
        return $users;
    }

    public static function createIfMissing(array $data): User
    {
        $user = User::findBy(
            $data['user_id'] ?? NULL,
            $data['email'] ?? NULL,
            $data['external_id'] ?? NULL
        );

        if (!$user) {
            $user = UserService::createUser($data);
        }
        return $user;
    }

    public static function updateUser(User $user, array $data): User
    {
        $data = array_except($data, ['email']);
        $user->update($data);

        if (isset($data['groups'])) {
            $user->groups()->sync($data['groups']);
        }
        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }
        $user->setMetadata(Meta::extractMetadata($data));

        return $user;
    }

    public static function bulkUpdateUsers(array $data): array
    {
        $users = [];
        foreach ($data['users'] as $userData) {
            $user = User::find($userData['id']);
            $users[] = self::updateUser($user, $userData);
        }
        return $users;
    }

    public static function generateActivationCode(): string
    {
        $code = str_random();
        while (User::where('activation_code', $code)->exists()) {
            $code = self::generateActivationCode();
        }
        return $code;
    }
}