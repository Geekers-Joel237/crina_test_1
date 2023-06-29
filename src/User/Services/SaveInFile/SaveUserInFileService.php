<?php

namespace App\User\Services\SaveInFile;

use App\User\SaveUserIn;
use App\User\Transform;
use Exception;

class SaveUserInFileService extends Singleton implements SaveUserIn
{
    private string $path = 'user_data.txt' ;
    private $fileHandle;

    public function __construct()
    {
        parent::__construct();
        $this->fileHandle =  fopen($this->path, 'a');
    }

    public function save(array $users): bool
    {

        try {
            foreach ($users as $user){
                $userToArray = Transform::toArray($user);
                $jsonString = json_encode($userToArray, JSON_PRETTY_PRINT);
                fwrite($this->fileHandle, $jsonString);
                fwrite($this->fileHandle, '**********');
            }
            fclose($this->fileHandle);
        }catch (Exception $e){
            echo 'Erreur lors de l\'ecriture dans le fichier '.$e->getMessage();
        }
        return true;
    }
}