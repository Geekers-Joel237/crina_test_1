<?php
namespace App;

use App\Task\Exceptions\CanNotAddTaskToSubTaskException;
use App\Task\Exceptions\TaskNotFoundException;
use App\Task\Task;
use App\Task\TaskRepository;
use App\Task\TaskStatus;
use App\User\Exceptions\NotEmptyException;
use App\User\Exceptions\UserNotFoundException;
use App\User\User;
use App\User\UserRepository;
use App\User\Vo\Id;

readonly class UserManageTasks
{

    public function __construct(
        private UserRepository $inMemoryUser,
        private TaskRepository $inMemoryTask
    )
    {
    }

    /**
     * @throws NotEmptyException
     * @throws UserNotFoundException
     * @throws TaskNotFoundException|CanNotAddTaskToSubTaskException
     */
    public function userCreateNewTask(
        User $user ,
        string $taskTitle ,
        string $taskDesc,
        ?Id $parentId): Task
    {
        self::checkIfUserIdExistOrThrowException($user);
        if($parentId){
            self::checkIfParentIdExistOrThrowException($parentId);
        }
        return Task::createTask(
            $taskTitle,
            $taskDesc,
            $user->getUserId(),
            $parentId
        );
    }

    /**
     * @throws UserNotFoundException
     */
    public function userMarkTaskHasFinished(User $user, Task $task):void
    {
        self::checkIfUserIdExistOrThrowException($user);
        $task->markTaskHasFinished();
        $this->checkIfAllSubtasksAreFinishedThenMarkParentTaskHasFinishedToo($task);
        $subTasks = $this->inMemoryTask->getSubTasks($task->getTaskId());
        foreach ($subTasks as $task){
            $task->markTaskHasFinished();
        }
    }

    /**
     * @throws UserNotFoundException
     */
    public function userMarkTasksListHasFinished(User $user, array $tasks): void
    {
        foreach ($tasks as $task){
            $this->userMarkTaskHasFinished($user, $task);
        }
    }

    /**
     * @throws UserNotFoundException
     */
    public function userMarkTaskHasDeleted(User $user, Task $task): void
    {
        self::checkIfUserIdExistOrThrowException($user);
        $task->delete();
        $subTasks = $this->inMemoryTask->getSubTasks($task->getTaskId());
        foreach ($subTasks as $task){
            $task->delete();
        }
    }

    /**
     * @throws UserNotFoundException
     */
    private  function checkIfUserIdExistOrThrowException(User $user): void
    {
        if (!$this->inMemoryUser->byId($user->getUserId())){
            throw new UserNotFoundException('C\'est utilisateur n\'existe pas');
        }
        self::checkIfUserIsLoggedIn($user);
    }

    /**
     * @throws TaskNotFoundException
     * @throws CanNotAddTaskToSubTaskException
     */
    private  function checkIfParentIdExistOrThrowException(Id $parentId): void
    {
        if (!$this->inMemoryTask->getTaskById($parentId)){
            throw new TaskNotFoundException('Cette tache n\'existe pas');
        }
        self::checkIfParentTaskIsSubTaskOrThrowException($parentId);
    }

    /**
     * @throws CanNotAddTaskToSubTaskException
     */
    private  function checkIfParentTaskIsSubTaskOrThrowException(Id $parentId): void
    {
        $parent = $this->inMemoryTask->getTaskById($parentId);
        if ($parent and $parent->getParentId()){
            $ancestorTask = $this->inMemoryTask->getTaskById($parent->getParentId());
                $ancestorTask->getParentId()?->value() ??
                throw new CanNotAddTaskToSubTaskException('Impossible d\'ajouter des  taches à une sous tache');
        }
    }

    /**
     * @param array $sameLevelTasks
     * @param array $parentSubTasks
     * @param Task $task
     * @return void
     */
    private function compareSizeBetweenSameLevelTasksArrayAndParentSubTasksArray(array $sameLevelTasks, array $parentSubTasks, Task $task): void
    {
        if (count($sameLevelTasks) === count($parentSubTasks)) {
            $this->markHasFinishedParentTask($task);
        }
    }

    /**
     * @param Task $task
     * @return void
     */
    private function markHasFinishedParentTask(Task $task): void
    {
        $this->inMemoryTask->getTaskById($task->getParentId())->markTaskHasFinished();
    }

    /**
     * @param Task $task
     * @return void
     */
    private function checkIfAllSubtasksAreFinishedThenMarkParentTaskHasFinishedToo(Task $task): void
    {
        if (!is_null($task->getParentId())) {
            $parentSubTasks = $this->inMemoryTask->getSubTasks($task->getParentId());
            $this->getSameLevelTasksForComparisonWithParentSubTasks($parentSubTasks, $task);
        }
    }

    /**
     * @param array $parentSubTasks
     * @param Task $task
     * @return void
     */
    private function getSameLevelTasksForComparisonWithParentSubTasks(array $parentSubTasks, Task $task): void
    {
        if (!empty($parentSubTasks)) {
            $sameLevelTasks = array_filter($parentSubTasks, fn(Task $t) => TaskStatus::FINISHED === $t->getStatus());
            $this->compareSizeBetweenSameLevelTasksArrayAndParentSubTasksArray($sameLevelTasks, $parentSubTasks, $task);
        }
    }

    /**
     * @throws UserNotFoundException
     */
    private static function checkIfUserIsLoggedIn(User $user): void
    {
        if (!$user->isLoggedIn()){
            throw new UserNotFoundException('C\'est Utilisateur n\'est pas connecté ');
        }
    }
}
