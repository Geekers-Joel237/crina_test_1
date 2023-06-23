<?php

namespace App\User\Vo;


use App\User\Exceptions\InvalidPhoneNumberException;

class Telephone
{
    private string $value;


    /**
     * @throws InvalidPhoneNumberException
     */
    public function __construct(
        string $value
    )
    {
        $this->validatePhoneNumberOrThrowException($value);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }


    /**
     * @throws InvalidPhoneNumberException
     */
    private  function validatePhoneNumberOrThrowException(string $value): void
    {
        $regex = "/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/";

        if(!preg_match($regex, $value)) {
            throw  new InvalidPhoneNumberException('Ce numero de telephone n\'est pas valide');
        }
    }
}