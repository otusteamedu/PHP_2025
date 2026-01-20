<?php
namespace App\Model;

use App\Database\Database;
use PDO;
use PDOException;

class State
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    
    /**
     * Получить состояние пользователя
     */
    public function getState($userId)
    {
        
        $stmt = $this->db->prepare("SELECT state FROM states WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $stmt->execute();
        $result= $stmt->fetch()['state'];
        return $result;
    }
    
    /**
     * Установить/обновить состояние пользователя
     */
    public function setState($userId, $state)
    {
        // file_put_contents('./logs.log', print_r($state,true), FILE_APPEND);
        // Проверяем, есть ли уже запись для пользователя
        $currentState = $this->getState($userId);
        
        if ($currentState === null) {
            // Вставляем новую запись
            $stmt = $this->db->prepare(
                "INSERT INTO states (user_id, state) VALUES (:user_id, :state)"
            );
        } else {
            // Обновляем существующую запись
            $stmt = $this->db->prepare(
                "UPDATE states SET state = :state WHERE user_id = :user_id"
            );
        }
        
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':state', $state, SQLITE3_TEXT);
        
        return $stmt->execute();
    }
    
    /**
     * Удалить состояние пользователя
     */
    public function deleteState($userId)
    {
        $stmt = $this->db->prepare("DELETE FROM states WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        
        return $stmt->execute();
    }
    
    /**
     * Получить всех пользователей в определенном состоянии
     */
    public function getUsersByState($state)
    {
        $stmt = $this->db->prepare("SELECT user_id FROM states WHERE state = :state");
        $stmt->bindValue(':state', $state, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        $users = [];
        // while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        //     $users[] = $row['user_id'];
        // }
        
        return $users;
    }
}