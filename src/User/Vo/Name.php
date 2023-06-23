<?php

namespace App\User\Vo;


use App\User\Exceptions\NotEmptyException;

class Name
{
    private string $value;

    /**
     * @throws NotEmptyException
     */
    public function __construct(
        string $value
    )
    {
        $this->isValid($value);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }


    /**
     * @throws NotEmptyException
     */
    private function isValid(string $value): void
    {
        if($value === '') {
            throw new NotEmptyException('Ce nom n\'est pas valide');
        }
    }
}