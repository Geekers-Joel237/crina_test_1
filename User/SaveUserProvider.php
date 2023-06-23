<?php

class SaveUserProvider
{
    public function __construct()
    {
    }

    public function saveInFile( $userJson): void
    {
        // Convert JSON data from an array to a string
        $jsonString = json_encode($userJson, JSON_PRETTY_PRINT);
        // Write in the file
        $path = 'user_data.txt' ;
        $fp = fopen($path, 'a');
        fwrite($fp, $jsonString);
        fwrite($fp, '**********');
        fclose($fp);
        // Generate txt file
    }

    public function getUsersList():array
    {
        $path = 'user_data.txt';
        $jsonString = file_get_contents($path);
        $data = explode('**********',$jsonString);
        var_dump($data);
        $jsonData = json_decode($jsonString, true);
        var_dump($jsonData);

        return [$jsonData];
    }

    public function saveInDb(array $userToArray): void
    {
        try {
            $database = new DbConnection();
            $db = $database->openConnection();

            $stm = $db->prepare("INSERT INTO users (user_id, first_name, last_name, email, password, telephone,is_logged_in)
                        VALUES ( :user_id, :first_name, :last_name, :email, :password, :telephone,false)") ;

            // inserting a record
            $stm->execute(array(
                ':user_id' => $userToArray['user_id'],
                ':first_name' => $userToArray['first_name'],
                ':last_name' => $userToArray['last_name'],
                ':email' => $userToArray['email'],
                ':password' => $userToArray['password'],
                ':telephone' => $userToArray['telephone']
            ));
            echo "New record created successfully";
        }catch(PDOException $e){
            echo "There is some problem in connection: " . $e->getMessage();
        }


    }

    public function saveLoggedUser(User $user): void
    {
        $userId = $user->getUserId();
        try
        {
            $database = new DbConnection();
            $db = $database->openConnection();
            $sql = "UPDATE `users` SET `is_logged_in`= true  WHERE `user_id` = '$userId'" ;
            $affected  = $db->exec($sql);

            if(isset($affected))
            {
                echo "Record has been successfully updated";
            }
        }
        catch (PDOException $e)
        {
            echo "There is some problem in connection: " . $e->getMessage();
        }
    }


}