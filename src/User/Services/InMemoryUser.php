<?php

namespace App\User\Services;;


use App\User\User;
use App\User\UserRepository;
use App\User\Vo\Email;
use App\User\Vo\Id;

class InMemoryUser implements UserRepository
{
    private array $users = [];
    public function __construct()
    {
    }

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    public function users(): array
    {
        return $this->users;
    }

    public function byId(Id $userId): ?User
    {
        $user = array_values(array_filter( $this->users, function (User $u) use ($userId) {
            return $userId->value() === $u->getUserId()->value();
        }));
        return $user[0] ?? null;
    }

    public function byEmail(Email $userEmail): ?User
    {
        $user = array_values(array_filter( $this->users, function (User $u) use ($userEmail) {
            return $userEmail->value() === $u->getEmail()->value();
        }));
        return $user[0] ?? null;
    }
}