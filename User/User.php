<?php

use Exceptions\InvalidEmailException;
use Exceptions\InvalidPasswordException;
use Task\Task;

class User
{
    private ?string $userId = null;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;
    private string $telephone;

    private bool $isLoggedIn = false;


    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param string $telephone
     */
    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $telephone
    )
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->telephone = $telephone;
    }

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @param bool $isLoggedIn
     */
    public function setIsLoggedIn(bool $isLoggedIn): void
    {
        $this->isLoggedIn = $isLoggedIn;
    }

    /**
     * @throws InvalidPasswordException
     * @throws InvalidEmailException
     */
    public static function createUser(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $telephone
    ): User{
        self::validateEmailOrThrowException($email);
        self::validatePasswordOrThrowException($password);
        self::validatePhoneNumberOrThrowException($telephone);
        $user =  new self(
            $firstName,
            $lastName,
            $email,
            hash('sha256',$password),
            $telephone
        );

        $user->userId ?? $user->setUserId(uniqid());
        return $user;
    }

    public function createTask(
        string $title,
        ?string $description,
        ?string $parentId
    ): Task
    {
        return Task::createTask(
            null,
            $title,
            $description,
            $this->getUserId(),
            $parentId
        );
    }

    public function markTaskHasFinished(Task $task): Task
    {
        $task->markTaskHasFinished();
        return $task;
    }

    public function markTaskHasPending(Task $task): Task
    {
        $task->markTaskHasPending();
        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }
    /**
     * @throws InvalidEmailException
     */
    private static function validateEmailOrThrowException(string $email): void
    {
        $email = filter_var($email,FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidEmailException('Cette adresse email n\'est pas valide');
        }
    }

    /**
     * @throws InvalidPasswordException
     */
    private static function validatePasswordOrThrowException(string $password): void
    {
        $regex = "/^(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&-]{8,}$/";
        if (!preg_match($regex, $password, $matches)) {
            throw new InvalidPasswordException('ce mot de passe n\'est pas valide');
        }
    }

    private static function validatePhoneNumberOrThrowException(string $telephone)
    {
    }

}