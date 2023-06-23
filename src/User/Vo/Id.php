<?php

namespace App\User\Vo;

class Id
{
    private string $id;

    public function __construct(
    )
    {
        $this->id = uniqid();
    }

    public function value(): string
    {
        return $this->id;
    }
}