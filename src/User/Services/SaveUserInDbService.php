<?php

namespace App\User\Services;

use App\User\SaveUserIn;

class SaveUserInDbService implements SaveUserIn
{

    public function save(array $users): bool
    {
        return false;
    }
}