<?php

namespace App\User\Vo;


use App\User\Exceptions\InvalidPasswordException;

class Password
{
    private string $value;


    /**
     * @throws InvalidPasswordException
     */
    public function __construct(
        string $value
    )
    {
        $this->validatePasswordOrThrowException($value);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function hash(): void
    {
        $this->value = hash('sha256',$this->value());
    }


    /**
     * @throws InvalidPasswordException
     */
    private  function validatePasswordOrThrowException(string $value): void
    {
        $regex = "/^(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&-]{8,}$/";
        if (!preg_match($regex, $value, $matches)) {
            throw new InvalidPasswordException('ce mot de passe n\'est pas valide');
        }
    }
}