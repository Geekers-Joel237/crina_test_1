<?php

namespace App\Task;

use App\User\Vo\Id;

interface TaskRepository
{
    public function saveTask(Task $task): void;

    public function getTasks(): array;

    public function getTaskById(Id $taskId);

    public function getSubTasks(Id $parentId);
}