<?php

namespace App\User\Vo;


use App\User\Exceptions\InvalidEmailException;

class Email
{
    private string $value;


    /**
     * @throws InvalidEmailException
     */
    public function __construct(
        string $value
    )
    {
        $this->validateEmailOrThrowException($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }


    /**
     * @throws InvalidEmailException
     */
    private  function validateEmailOrThrowException(string $value): void
    {
        $email = filter_var($value,FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidEmailException('Cette adresse email n\'est pas valide');
        }
    }
}