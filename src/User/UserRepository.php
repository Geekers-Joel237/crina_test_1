<?php

namespace App\User;

use App\User\Vo\Email;
use App\User\Vo\Id;

interface UserRepository
{
    public function save(User $user);

    public function users(): array;

    public function byId(Id $userId): ?User;
    public function byEmail(Email $userEmail): ?User;
}