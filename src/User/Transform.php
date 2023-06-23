<?php

namespace App\User;

class Transform
{
    public static function toArray(User $user): array
    {
        return [
            'user_id' => $user->getUserId()->value(),
            'first_name' => $user->getFirstName()->value(),
            'last_name' => $user->getLastName()->value(),
            'email' => $user->getEmail()->value(),
            'password' => $user->getPassword()->value(),
            'telephone' => $user->getTelephone()->value(),
            'is_logged' => $user->isLoggedIn()
        ];
    }
}