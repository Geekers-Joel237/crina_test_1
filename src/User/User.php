<?php

namespace App\User;



use App\User\Exceptions\InvalidEmailException;
use App\User\Exceptions\InvalidPasswordException;
use App\User\Exceptions\InvalidPhoneNumberException;
use App\User\Exceptions\NotEmptyException;
use App\User\Vo\Email;
use App\User\Vo\Id;
use App\User\Vo\Name;
use App\User\Vo\Password;
use App\User\Vo\Telephone;

class User
{
    private ?Id $userId = null;
    private Name $firstName;
    private Name $lastName;
    private Email $email;
    private Password $password;
    private Telephone $telephone;

    private bool $isLoggedIn = false;

    /**
     * @param Name $firstName
     * @param Name $lastName
     * @param Email $email
     * @param Password $password
     * @param Telephone $telephone
     */
    public function __construct(
        Name $firstName,
        Name $lastName,
        Email $email,
        Password $password,
        Telephone $telephone
    )
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->telephone = $telephone;
    }


    /**
     * @throws NotEmptyException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidPhoneNumberException
     */
    public static function createUser(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $telephone
    ): User{
        $user =  new self(
            new Name($firstName),
            new Name($lastName),
            new Email($email),
            new Password($password),
            new Telephone($telephone)
        );

            $user->password->hash();
            $user->userId ?? $user->setUserId();
        return $user;
    }

    /**
     * @return Id|null
     */
    public function getUserId(): ?Id
    {
        return $this->userId;
    }

    /**
     * @return Name
     */
    public function getFirstName(): Name
    {
        return $this->firstName;
    }

    /**
     * @return Name
     */
    public function getLastName(): Name
    {
        return $this->lastName;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return Password
     */
    public function getPassword(): Password
    {
        return $this->password;
    }

    /**
     * @return Telephone
     */
    public function getTelephone(): Telephone
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
     */
    public function setIsLoggedIn(): void
    {
        $this->isLoggedIn = true;
    }

    private function setUserId(): void
    {
        $this->userId = new Id();
    }


}