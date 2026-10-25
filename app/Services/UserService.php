<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()->orderBy('name')->paginate($perPage);
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): User
    {
        return DB::transaction(static fn (): User => User::query()->create($data));
    }

    /** @param array<string, mixed> $data */
    public function update(User $user, array $data): User
    {
        if (($data['password'] ?? null) === null || $data['password'] === '') {
            unset($data['password']);
        }

        return DB::transaction(static function () use ($user, $data): User {
            $user->update($data);

            return $user->refresh();
        });
    }
}
