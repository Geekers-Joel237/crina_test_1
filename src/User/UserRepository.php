<?php

namespace App\User;

use App\User\Vo\Id;

interface UserRepository
{
    public function save(User $user);

    public function users(): array;

    public function getUserById(Id $userId): ?User;
}