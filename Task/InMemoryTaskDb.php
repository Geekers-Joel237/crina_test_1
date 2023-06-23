<?php

namespace Task;

class InMemoryTaskDb
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
                return  $t->getTaskId() === $task->getTaskId();
            }
            )
        );

        if(!isset($existingTasks[0])){
            $this->tasks[] = $task;
        }

        $this->tasks = array_map(function (Task $t) use ($task) {
           if ($t->getTaskId() === $task->getTaskId()) {
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

    public function getTaskById(string $taskId): ?Task
    {
        $task = array_values(array_map(function (Task $t) use ($taskId) {
            return $taskId === $t->getTaskId();
        }, $this->tasks));

        return $task[0];
    }

}