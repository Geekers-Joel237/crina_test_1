<?php

namespace App\Task;

use App\User\Vo\Id;

class InMemoryTask
{
    private array $tasks = [];

    public function __construct()
    {
    }

    public function saveTask(Task $task): void
    {
        if(empty($this->tasks)){
            $this->tasks[] = $task;
        }
        $existingTasks =array_values(
            array_filter($this->tasks, function (Task $t) use ($task) {
                return  $t->getTaskId()->value() === $task->getTaskId()->value();
            }
            )
        );

        if(!isset($existingTasks[0])){
            $this->tasks[] = $task;
        }

        $this->tasks = array_map(function (Task $t) use ($task) {
            if ($t->getTaskId()->value() === $task->getTaskId()->value()) {
                return $task;
            }
            return $t;
        },$this->tasks);

    }

    /**
     * @return array
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function getTaskById(Id $taskId): ?Task
    {
        $task = array_values(array_filter($this->tasks, function (Task $t) use ($taskId) {
            return $taskId->value() === $t->getTaskId()->value();
        }));
        return $task[0] ?? null;
    }

    public function getSubTasks(Id $parentId): array
    {
        return array_filter( $this->tasks, function (Task $t) use($parentId){
            return $t->getParentId()?->value() === $parentId->value();
        });
    }

    public function saveAll(array $tasks): void
    {
        foreach ($tasks as $task){
            $this->saveTask($task);
        }
    }

}