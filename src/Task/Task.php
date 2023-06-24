<?php

namespace App\Task;

use App\User\Exceptions\NotEmptyException;
use App\User\Vo\Id;
use App\User\Vo\Name;

class Task
{
    private ?Id $taskId = null;
    private Name $title ;
    private ?Name $description = null;

    private TaskStatus $status;

    private Id $userId;
    private ?Id $parentId = null;

    private bool $isDeleted = false;

    public function __construct(
        Name $title,
        Id $userId,
        TaskStatus $status
    )
    {
        $this->title = $title;
        $this->userId = $userId;
        $this->status = $status;
    }

    /**
     * @throws NotEmptyException
     */
    public static function createTask(
        string $title,
        ?string $description,
        Id $userId,
        ?Id $parentId
    ): Task
    {
        $task = new self(
            new Name($title),
            $userId,
            TaskStatus::PENDING
        );

        $task->setIsDeleted(false);
        if ($description){
            $task->setDescription(new Name($description));
        }
        if ($parentId){
            $task->setParentId($parentId);
        }

            $taskId ?? $task->setTaskId();
        return $task;
    }

    /**
     * @return Id|null
     */
    public function getTaskId(): ?Id
    {
        return $this->taskId;
    }

    /**
     * @return Name
     */
    public function getTitle(): Name
    {
        return $this->title;
    }

    /**
     * @return Name|null
     */
    public function getDescription(): ?Name
    {
        return $this->description;
    }

    /**
     * @return TaskStatus
     */
    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    /**
     * @return Id
     */
    public function getUserId(): Id
    {
        return $this->userId;
    }

    /**
     * @return Id|null
     */
    public function getParentId(): ?Id
    {
        return $this->parentId;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }
    /**
     * @param Name|null $description
     */
    public function setDescription(?Name $description): void
    {
        $this->description = $description;
    }

    /**
     * @param Id $parentId
     */
    public function setParentId(Id $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @param TaskStatus $status
     */
    public function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public  function markTaskHasFinished(): Task
    {
        $this->setStatus(TaskStatus::FINISHED);
        return $this;
    }

    public function markTaskHasPending(): Task
    {
        $this->setStatus(TaskStatus::PENDING);
        return $this;
    }

    public function delete(): void
    {
        $this->setIsDeleted('true');
    }

    private function setTaskId(): void
    {
        $this->taskId = new Id();
    }



}