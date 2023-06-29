<?php

namespace App\User\Services;

use App\User\Exceptions\CredentialsNotMatchException;
use App\User\Exceptions\InvalidEmailException;
use App\User\UserRepository;
use App\User\Vo\Email;

readonly class AuthUserService
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {

    }

    /**
     * @throws InvalidEmailException
     * @throws CredentialsNotMatchException
     */
    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->byEmail(new Email($email));
        if (!is_null($user)) {
            if ($user->getPassword()->value() === $password){
                $user->setIsLoggedIn();
                $this->userRepository->save($user);
                return $user->isLoggedIn();
            }
            throw new CredentialsNotMatchException('Email ou Mot de Passe Incorrect');
        }
        return false;
    }
}