<?php

use Exceptions\CredentialsNotValidException;
use Exceptions\InvalidEmailException;
use Exceptions\InvalidPasswordException;
use Task\InMemoryTaskDb;
use Task\SaveTaskProvider;
use Task\Task;

require './User/Exceptions/InvalidEmailException.php';
require './User/Exceptions/InvalidPasswordException.php';
require './User/Exceptions/CredentialsNotValidException.php';
require './User/User.php';
require './User/SaveUserProvider.php';
require './DbConnection.php';
require  './User/AuthUser.php';
require  './User/Transform.php';
require './Task/TaskStatus.php';
require  './Task/Task.php';
require  './Task/SaveTaskProvider.php';
require './Task/InMemoryTaskDb.php';
readonly class Main
{
    private InMemoryTaskDb $db;

    public function __construct()
    {
        $this->db = new InMemoryTaskDb();
    }

    public function tasks():array
    {
        return $this->db->getTasks();
    }
    /**
     * @throws InvalidPasswordException
     * @throws InvalidEmailException
     */
    public function main_can_create_user(
        string $firstName,
        string $lastName,
    ): User
    {
        return User::createUser(
            $firstName,
            $lastName,
            $firstName.$lastName.'@gmail.com',
            'crina@2023',
            '+237 673415289'
        );
    }

    public function main_can_convert_user_to_array(User $user): array
    {
        return Transform::toArray($user);
    }

    public function main_can_save_user_in_file(array $userToArray): void
    {
        $saveUserProvider = new SaveUserProvider();
        $saveUserProvider->saveInFile($userToArray);
    }

    public function main_can_save_user_in_db(array $userToArray): void{
        $saveUserProvider = new SaveUserProvider();
        $saveUserProvider->saveInDb($userToArray);
    }

    /**
     * @throws CredentialsNotValidException
     */
    public function main_can_logged_in_user(User $user): User | null
    {
        $auth = new AuthUser();
        return $auth->login($user->getEmail(),$user->getPassword());
    }

    public function main_can_save_user_has_logged(User $user): void
    {
        $saveUserProvider = new SaveUserProvider();
        $saveUserProvider->saveLoggedUser($user);
    }

    public function main_can_save_task(array $tasks): void
    {
        $saveTaskProvider = new SaveTaskProvider();
        $saveTaskProvider->saveInFile($tasks);

    }

    public function main_can_save_task_in_memory(Task $task): array
    {
        $this->db->saveTask($task);
        return $this->db->getTasks();
    }
}

$main = new Main();

try {
    $user1 = $main->main_can_create_user(
        'John','Doe'
    );
    $user2 = $main->main_can_create_user(
        'Jane','Doe'
    );
    $user3 = $main->main_can_create_user(
        'test','test'
    );

    $user1ToArray = $main->main_can_convert_user_to_array($user1);
    $user2ToArray = $main->main_can_convert_user_to_array($user2);
    $user3ToArray = $main->main_can_convert_user_to_array($user3);

    $main->main_can_save_user_in_file($user1ToArray);
    //$main->main_can_save_user_in_db($user1ToArray);

    $main->main_can_save_user_in_file($user2ToArray);
    //$main->main_can_save_user_in_db($user2ToArray);

    $userLogged = $main->main_can_logged_in_user($user1);

    if (!is_null($userLogged)){
        $main->main_can_save_user_has_logged($userLogged);
        $task1 = $user1->createTask(
            'task1',
            null,
            null
        );

        $task2 = $user1->createTask(
            'task2',
            'description task2',
            $task1->getTaskId()
        );

       // $main->main_can_save_task([$task1,$task2]);
        $main->main_can_save_task_in_memory($task1);
        $main->main_can_save_task_in_memory($task2);
        $user1->markTaskHasFinished($task2);
        $main->main_can_save_task_in_memory($task2);

        var_dump($main->tasks());
        $saveTask = new SaveTaskProvider();
        $saveTask->saveInFile($main->tasks());

    }else{
        echo 'nice';
    }


} catch (InvalidEmailException|InvalidPasswordException|CredentialsNotValidException $e) {
    echo $e->getMessage();
}

