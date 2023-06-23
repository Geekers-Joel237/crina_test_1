<?php

use Exceptions\CredentialsNotValidException;

class AuthUser
{

    /**
     * @throws CredentialsNotValidException
     */
    public function login(string $email, string $password): ?User
    {
        if ($email === '' || $password === ''){
            throw new CredentialsNotValidException('Email ou Mot de passe non valide ');
        }
        $database = new DbConnection();
        $db = $database->openConnection();

        $stm = $db->prepare("SELECT * FROM users WHERE email=:email AND password=:password LIMIT 1");
        $stm->execute(array(
            ':email' => $email,
            ':password' => $password,
        ));
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        if (!$row){
            throw new CredentialsNotValidException('Email ou Mot de passe non trouve');
        }
        if(count($row) > 0){
            $user = $this->toUser($row);
            $user->setIsLoggedIn(true);
            return  $user;
        }
        return null;
    }

    private function toUser(array $row): User
    {
        $user = new User(
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['password'],
            $row['telephone'],
        );
        $user->setUserId($row['user_id']);
        return $user;
    }
}