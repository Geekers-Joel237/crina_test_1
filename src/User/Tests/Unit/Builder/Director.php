<?php

namespace App\User\Tests\Unit\Builder;

class Director
{
    private static $builder;
    public static function build(): UserBuilder
    {
        self::$builder = new UserBuilderSUT();
        return self::$builder;
    }
}