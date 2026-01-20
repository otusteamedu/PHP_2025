<?php

namespace App\Controller;

use App\Model\State;

class StateController
{
    private $state;
    
    public function __construct()
    {
        $this->state = new State();
    }
    
    /**
     * Установить состояние для пользователя
     */
    public function setUserState($userId, $stateName)
    {
        return $this->state->setState($userId, $stateName);
    }
    
    /**
     * Получить текущее состояние пользователя
     */
    public function getUserState($userId)
    {
        return $this->state->getState($userId);
    }
    
    /**
     * Сбросить состояние пользователя
     */
    public function resetUserState($userId)
    {
        return $this->state->deleteState($userId);
    }
    
    /**
     * Проверить, находится ли пользователь в определенном состоянии
     */
    public function isUserInState($userId, $stateName)
    {
        $currentState = $this->state->getState($userId);
        return $currentState === $stateName;
    }
    
    /**
     * Получить всех пользователей в определенном состоянии
     */
    public function getUsersInState($stateName)
    {
        return $this->state->getUsersByState($stateName);
    }
    
    /**
     * Переключить состояние пользователя
     */
    public function switchUserState($userId, $newState)
    {
        return $this->state->setState($userId, $newState);
    }
}