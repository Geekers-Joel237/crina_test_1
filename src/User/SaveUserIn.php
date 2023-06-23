<?php

namespace App\User;
interface SaveUserIn
{
    public function save(array $users): bool;
}