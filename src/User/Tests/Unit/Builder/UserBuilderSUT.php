<?php

namespace App\User\Tests\Unit\Builder;

use App\User\Exceptions\InvalidEmailException;
use App\User\Exceptions\InvalidPasswordException;
use App\User\Exceptions\InvalidPhoneNumberException;
use App\User\Exceptions\NotEmptyException;
use App\User\User;

class UserBuilderSUT implements UserBuilder
{
    private User $user;

    public function isLoggedIn(): UserBuilder
    {
        $this->user->setIsLoggedIn();
        return $this;
    }

    /**
     * @throws NotEmptyException
     * @throws InvalidEmailException
     * @throws InvalidPhoneNumberException
     * @throws InvalidPasswordException
     */
    public function createUser(): UserBuilder
    {
        $this->user = User::createUser(
            firstName: 'John',
            lastName: 'Doe',
            email: 'JohnDoe@gmail.com',
            password: 'crina@2023',
            telephone: '237-1234-1234'
        );
        return $this;
    }

    public function user(): User
    {
        return $this->user;
    }
}