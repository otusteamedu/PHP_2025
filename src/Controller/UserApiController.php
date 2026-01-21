<?php

namespace App\Controller;

use App\Model\User;
use Exception;

class UserApiController
{
    /**
     * DELETE /api/users/bitrix/{bitrixId}
     */
    public function deleteByBitrixApi(int $bitrixId): array
    {
        try {
            $deleted = User::deleteByBitrixId($bitrixId);
            
            if (!$deleted) {
                return [
                    'success' => false,
                    'error' => 'User not found',
                    'status' => 404
                ];
            }
            
            return [
                'success' => true,
                'message' => 'User deleted successfully',
                'status' => 200
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    
    /**
     * POST /api/users
     */
    public function create(array $data): array
    {
        try {
            // Валидация обязательных полей
            if (empty($data['bitrix_id'])) {
                return [
                    'success' => false,
                    'error' => 'bitrix_id is required',
                    'status' => 400
                ];
            }
            
            // Преобразуем данные в формат, ожидаемый User::createOrUpdate
            $userData = [
                'bitrix_id' => (int) $data['bitrix_id'],
                'fio' => $data['fio'] ?? null,
                'username' => $data['username'] ?? null,
            ];
            
            $user = User::createOrUpdate($userData);
            
            return [
                'success' => true,
                'data' => [
                    'id' => $user->getId(),
                    'bitrix_id' => $user->getBitrixId(),
                    'fio' => $user->getFIO(),                    
                    'username' => $user->getUsername(),
                ],
                'status' => 201
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    
    /**
     * PUT /api/users/bitrix/{bitrixId}
     */
    public function updateByBitrixId(int $bitrixId, array $data): array
    {
        try {
            // Проверяем существование пользователя
            $existingUser = User::findByBitrixId($bitrixId);            
            
            if (!$existingUser) {
                return [
                    'success' => false,
                    'error' => 'User not found',
                    'status' => 404
                ];
            }
            
            // Подготавливаем данные для обновления
            $updateData = [               
                'bitrix_id' => $bitrixId, // bitrix_id не меняется
                'fio' => $data['fio'] ?? $existingUser->getFIO(),
                'username' => $data['username'] ?? $existingUser->getUsername(),
            ];
            
            $user = User::createOrUpdate($updateData);
            
            return [
                'success' => true,
                'data' => [
                    'id' => $user->getId(),
                    'bitrix_id' => $user->getBitrixId(),
                    'fio' => $user->getFIO(),                    
                    'username' => $user->getUsername(),
                ],
                'status' => 200
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
    
    /**
     * Дополнительный метод для получения всех пользователей
     * GET /api/users
     */
    public function getAll(): array
    {
        try {
            $users = User::getAllUsers();
            
            $result = [];
            foreach ($users as $user) {
                $result[] = [
                    'id' => $user->getId(),
                    'bitrix_id' => $user->getBitrixId(),
                    'fio' => $user->getFIO(),                    
                    'username' => $user->getUsername(),
                ];
            }
            
            return [
                'success' => true,
                'data' => $result,
                'count' => User::countUsers(),
                'status' => 200
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }
}