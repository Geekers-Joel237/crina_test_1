<?php

namespace Tests\Unit;

use App\Task\Exceptions\CanNotAddTaskToSubTaskException;
use App\Task\Exceptions\TaskNotFoundException;
use App\Task\InMemoryTask;
use App\Task\TaskStatus;
use App\User\Exceptions\InvalidEmailException;
use App\User\Exceptions\InvalidPasswordException;
use App\User\Exceptions\InvalidPhoneNumberException;
use App\User\Exceptions\NotEmptyException;
use App\User\Exceptions\UserNotFoundException;
use App\User\Services\AuthUserService;
use App\User\Services\InMemoryUser;
use App\User\Services\SaveInFile\SaveUserInFileService;
use App\User\Services\SaveInFile\Singleton;
use App\User\Tests\Unit\Builder\Director;
use App\User\User;
use App\UserManageTasks;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private InMemoryUser $inMemoryUser;

    private InMemoryTask $inMemoryTask;
    private Singleton $userFileService;
    private AuthUserService $authService;

    public function setUp(): void
    {
        parent::setUp();
        $this->buildUserSUT();
        $this->inMemoryUser = new InMemoryUser();
        $this->userFileService = new SaveUserInFileService();
        $this->authService = new AuthUserService($this->inMemoryUser);
        $this->inMemoryTask = new InMemoryTask();
    }


    /**
     * @throws NotEmptyException
     * @throws InvalidEmailException
     * @throws InvalidPhoneNumberException
     * @throws InvalidPasswordException
     */
    private function createCustomUser(
        string $firstName,
        string $lastName,
    ): User
    {
        return User::createUser(
            $firstName,
            $lastName,
            $firstName . $lastName . '@gmail.com',
            'crina@2023',
            '237-1234-1234'
        );
    }

    public function test_can_create_user()
    {
        //Arrange //Act //Assert
        //When //Given //Then
        $user1 = $this->createCustomUser(
            'John', 'Doe'
        );
        $user2 = $this->createCustomUser(
            'Jane', 'Doe'
        );
        $user3 = $this->createCustomUser(
            'test', 'test'
        );

        $this->assertNotNull($user1);
        $this->assertEquals('JohnDoe@gmail.com',$user1->getEmail()->value());
        $this->assertNotNull($user2);
        $this->assertNotNull($user3);
    }

    public function test_can_save_user_in_memory()
    {
        $this->inMemoryUser->save($this->buildUserSUT()[0]);

        $users = $this->inMemoryUser->users();
        $this->assertCount(1,$users);
        $this->assertEquals('JohnDoe@gmail.com',$users[0]->getEmail()->value());

    }

    public function test_can_save_user_in_file()
    {
        $response = $this->userFileService::getInstance()->save($this->buildUserSUT());
        $this->assertTrue($response);
    }

    public function test_can_logged_user()
    {
        $user1 = $this->buildUserSUT()[0];
        $this->inMemoryUser->save($user1);
        $response = $this->authService
            ->login($user1->getEmail()->value(),$user1->getPassword()->value());

        $this->assertTrue($response);
    }

    /**
     * @throws NotEmptyException
     * @throws UserNotFoundException
     * @throws TaskNotFoundException
     * @throws CanNotAddTaskToSubTaskException
     */
    public function test_can_create_task()
    {
        $user = Director::build()->createUser()->isLoggedIn()->user();
        $this->inMemoryUser->save($user);

        $manager = new UserManageTasks($this->inMemoryUser, $this->inMemoryTask);
        $task = $manager->userCreateNewTask(
         $user,
         'Task 1',
         'Task 1 description',
         null
        );

        $this->assertNotNull($task);
        $this->assertEquals('Task 1', $task->getTitle()->value());
        $this->assertNotNull( $task->getUserId()->value());
        $this->assertTrue($user->isLoggedIn());


    }

    /**
     * @throws NotEmptyException
     * @throws UserNotFoundException
     * @throws TaskNotFoundException
     * @throws CanNotAddTaskToSubTaskException
     */
    public function test_can_save_task_in_memory()
    {
        $user =Director::build()->createUser()->isLoggedIn()->user();
        $this->inMemoryUser->save($user);

        $manager = new UserManageTasks($this->inMemoryUser, $this->inMemoryTask);
        $task = $manager->userCreateNewTask(
            $user,
            'Task 1',
            'Task 1 description',
            null
        );

        $this->inMemoryTask->saveTask($task);
        $this->assertCount(1, $this->inMemoryUser->users());

    }

    /**
     * @throws NotEmptyException
     * @throws UserNotFoundException
     * @throws TaskNotFoundException
     * @throws CanNotAddTaskToSubTaskException
     */
    public function test_can_save_task_with_sub_tasks_in_memory()
    {
        $user = Director::build()->createUser()->isLoggedIn()->user();
        $this->inMemoryUser->save($user);

        $manager = new UserManageTasks($this->inMemoryUser, $this->inMemoryTask);
        $task1 = $manager->userCreateNewTask(
            $user,
            'Task 1',
            'Task 1 description',
            null
        );
        $this->inMemoryTask->saveTask($task1);
        $task2 = $manager->userCreateNewTask(
            $user,
            'Task 2',
            'Task 2 description',
            $task1->getTaskId()
        );
        $this->inMemoryTask->saveTask($task2);

        $this->assertCount(2,$this->inMemoryTask->getTasks());
        $this->assertEquals($task1->getTaskId()->value(),$task2->getParentId()->value());
    }

    /**
     * @throws NotEmptyException
     * @throws TaskNotFoundException
     * @throws UserNotFoundException
     */
    public function test_can_throw_cannot_add_task_to_sub_task_exception()
    {
        $this->expectException(CanNotAddTaskToSubTaskException::class);
        $user = Director::build()->createUser()->isLoggedIn()->user();
        $this->inMemoryUser->save($user);

        $manager = new UserManageTasks($this->inMemoryUser, $this->inMemoryTask);
        $task1 = $manager->userCreateNewTask(
            $user,
            'Task 1',
            'Task 1 description',
            null
        );
        $this->inMemoryTask->saveTask($task1);
        $task2 = $manager->userCreateNewTask(
            $user,
            'Task 2',
            'Task 2 description',
            $task1->getTaskId()
        );
        $this->inMemoryTask->saveTask($task2);

        $task3 = $manager->userCreateNewTask(
            $user,
            'Task 3',
            'Task 3 description',
            $task2->getTaskId()
        );

    }

    /**
     * @throws NotEmptyException
     * @throws CanNotAddTaskToSubTaskException
     * @throws UserNotFoundException
     * @throws TaskNotFoundException
     */
    public function test_can_finished_task_and_his_sub_tasks()
    {
        list($user, $manager, $task1, $task2, $task3) = $this->extracted();
        $this->inMemoryTask->saveTask($task3);
        $manager->userMarkTaskHasFinished($user, $task1);
        $this->inMemoryTask->saveTasks([$task1,$task2,$task3]);

        $this->assertEquals(TaskStatus::FINISHED->value(),$task1->getStatus()->value());
        $this->assertEquals(TaskStatus::FINISHED->value(),$task2->getStatus()->value());
        $this->assertEquals(TaskStatus::FINISHED->value(),$task3->getStatus()->value());

    }

    public function test_can_delete_task_and_in_substasks()
    {
        $user = Director::build()->createUser()->isLoggedIn()->user();
        $this->inMemoryUser->save($user);

        $manager = new UserManageTasks($this->inMemoryUser, $this->inMemoryTask);
        $task1 = $manager->userCreateNewTask(
            $user,
            'Task 1',
            'Task 1 description',
            null
        );
        $this->inMemoryTask->saveTask($task1);
        $task2 = $manager->userCreateNewTask(
            $user,
            'Task 2',
            'Task 2 description',
            $task1->getTaskId()
        );
        $this->inMemoryTask->saveTask($task2);

        $task3 = $manager->userCreateNewTask(
            $user,
            'Task 3',
            'Task 3 description',
            $task1->getTaskId()
        );
        $this->inMemoryTask->saveTask($task3);
        $manager->userMarkTaskHasDeleted($user, $task1);
        $this->inMemoryTask->saveTasks([$task1,$task2,$task3]);

        $this->assertTrue($task1->isDeleted());
        $this->assertTrue($task2->isDeleted());
        $this->assertTrue($task3->isDeleted());
    }

    public function test_can_auto_finish_principal_task_when_all_subtasks_is_finished()
    {
        $user = Director::build()->createUser()->isLoggedIn()->user();;
        $this->inMemoryUser->save($user);

        $manager = new UserManageTasks($this->inMemoryUser, $this->inMemoryTask);
        $task1 = $manager->userCreateNewTask(
            $user,
            'Task 1',
            'Task 1 description',
            null
        );
        $this->inMemoryTask->saveTask($task1);
        $task2 = $manager->userCreateNewTask(
            $user,
            'Task 2',
            'Task 2 description',
            $task1->getTaskId()
        );
        $this->inMemoryTask->saveTask($task2);

        $task3 = $manager->userCreateNewTask(
            $user,
            'Task 3',
            'Task 3 description',
            $task1->getTaskId()
        );
        $this->inMemoryTask->saveTask($task3);
        $manager->userMarkTasksListHasFinished($user, [$task2, $task3]);
        $this->inMemoryTask->saveTasks([$task2,$task3]);

        $this->assertEquals(TaskStatus::FINISHED->value(),$task1->getStatus()->value());
        $this->assertEquals(TaskStatus::FINISHED->value(),$task2->getStatus()->value());
        $this->assertEquals(TaskStatus::FINISHED->value(),$task3->getStatus()->value());
    }
    public function buildUserSUT(): array
    {
        $users = [];

        $user1 = $this->createCustomUser(
            'John', 'Doe'
        );
        $user2 = $this->createCustomUser(
            'Jane', 'Doe'
        );
        $user3 = $this->createCustomUser(
            'test', 'test'
        );

        return $users = [$user1, $user2, $user3];
    }

    /**
     * @return array
     * @throws CanNotAddTaskToSubTaskException
     * @throws NotEmptyException
     * @throws TaskNotFoundException
     * @throws UserNotFoundException
     */
    public function extracted(): array
    {
        $user = Director::build()->createUser()->isLoggedIn()->user();
        $this->inMemoryUser->save($user);

        $manager = new UserManageTasks($this->inMemoryUser, $this->inMemoryTask);
        $task1 = $manager->userCreateNewTask(
            $user,
            'Task 1',
            'Task 1 description',
            null
        );
        $this->inMemoryTask->saveTask($task1);
        $task2 = $manager->userCreateNewTask(
            $user,
            'Task 2',
            'Task 2 description',
            $task1->getTaskId()
        );
        $this->inMemoryTask->saveTask($task2);

        $task3 = $manager->userCreateNewTask(
            $user,
            'Task 3',
            'Task 3 description',
            $task1->getTaskId()
        );
        return array($user, $manager, $task1, $task2, $task3);
    }
}