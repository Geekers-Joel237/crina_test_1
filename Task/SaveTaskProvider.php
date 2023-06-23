<?php

namespace Task;

use DbConnection;
use PDOException;

class SaveTaskProvider
{

    public function deleteTask(string $taskId): void
    {
        try
        {
            $database = new DbConnection();
            $db = $database->openConnection();
            $sql = "DELETE FROM tasks WHERE `task_id` = '$taskId'" ;
            $affectedRows  = $db->exec($sql);
            if(isset($affectedRows))
            {
                echo "Record has been successfully deleted";
            }
        }
        catch (PDOException $e)
        {
            echo "There is some problem in connection: " . $e->getMessage();
        }

    }

    public function saveInFile(array $tasks): void
    {
        $path = 'task_data.txt' ;
        $fp = fopen($path, 'w');
        foreach ($tasks as $task){
            $task = $this->toArray($task);
            $jsonString = json_encode($task, JSON_PRETTY_PRINT);
            fwrite($fp, $jsonString);
            fwrite($fp, '**********');
            // Generate txt file
        }
        fclose($fp);

    }

    private function toArray(Task $task): array
    {
        return [
          'task_id' => $task->getTaskId(),
          'title' => $task->getTitle(),
          'description' => $task->getDescription()?? null,
          'user_id' => $task->getUserId(),
          'parent_id' => $task->getParentId()?? null,
          'status' => $task->getStatus()->value()
        ];
    }
}