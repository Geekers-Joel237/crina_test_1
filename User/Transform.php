<?php

class Transform
{

    public static function toArray(User $user): array
    {
        return [
            'user_id' => $user->getUserId(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'telephone' => $user->getTelephone()
        ];
    }
}