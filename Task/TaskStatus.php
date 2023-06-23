<?php

namespace Task;

enum TaskStatus: int
{
    case PENDING = 0;
    case FINISHED = 1;

    public function value(): int
    {
        return $this->value;
    }
}
