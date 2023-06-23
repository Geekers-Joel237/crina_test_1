<?php

namespace Task;

class Task
{
    private ?string $taskId = null;
    private string $title ;

    private ?string $description = null;

    private TaskStatus $status;
    private string $userId;
    private ?string $parentId = null;

    private bool $isDeleted = false;


    public function __construct(
        string $title,
        string $userId,
        TaskStatus $status
    )
    {
        $this->title = $title;
        $this->userId = $userId;
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @return string|null
     */
    public function getTaskId(): ?string
    {
        return $this->taskId;
    }

    /**
     * @param string|null $taskId
     */
    public function setTaskId(?string $taskId): void
    {
        $this->taskId = $taskId;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * @param string $parentId
     */
    public function setParentId(string $parentId): void
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return TaskStatus
     */
    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }
    public static function createTask(
        ?string $taskId,
        string $title,
        ?string $description,
        string $userId,
        ?string $parentId
    ): Task
    {
        $task = new self(
            $title,
            $userId,
            TaskStatus::PENDING
        );

        $task->setIsDeleted(false);
        if ($description){
            $task->setDescription($description);
        }

        if(!empty($parentId)){
            self::checkIfParentIdExistOrThrowException($parentId);
            $task->setParentId($parentId);
        }

        $taskId ?? $task->setTaskId(uniqid());
        return $task;
    }

    public static function deleteTask(Task $task): Task
    {
        $task->setIsDeleted(true);
        return $task;
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

    private static function checkIfParentTaskIsSubTaskOrThrowException(string $parentId)
    {
    }


    private static function checkIfParentIdExistOrThrowException(string $parentId): void
    {

        self::checkIfParentTaskIsSubTaskOrThrowException($parentId);

    }

}