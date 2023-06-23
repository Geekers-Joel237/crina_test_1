<?php

namespace App\User\Services;

use App\User\SaveUserIn;
use App\User\Transform;
use Exception;

class SaveUserInFileService implements SaveUserIn
{

    public function __construct()
    {
    }

    public function save(array $users): bool
    {
        $path = 'user_data.txt' ;

        try {
            $fp = fopen($path, 'a');
            foreach ($users as $user){
                $userToArray = Transform::toArray($user);
                $jsonString = json_encode($userToArray, JSON_PRETTY_PRINT);
                fwrite($fp, $jsonString);
                fwrite($fp, '**********');
            }
            fclose($fp);
        }catch (Exception $e){
            echo 'Erreur lors de l\'ecriture dans le fichier '.$e->getMessage();
        }
        return true;
    }
}