<?php

namespace App\User\Tests\Unit\Builder;

use App\User\User;

interface UserBuilder
{
    public function isLoggedIn(): self;

    public function createUser(): self;

    public function user(): User;
}