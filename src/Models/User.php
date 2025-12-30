<?php

namespace App\Models;

use App\Collections\Collection;
use PDO;

/**
 * Модель для таблицы users
 * Здесь работает паттерн Active Record с Identity Map
 */
class User
{
    // Identity Map - хранит загруженные объекты по ID
    private static array $identityMap = [];
    
    private ?int $id = null;
    private string $name;
    private string $email;
    
    public function __construct(string $name = '', string $email = '')
    {
        $this->name = $name;
        $this->email = $email;
    }
    
    /**
     * Получить всех пользователей - возвращает коллекцию
     */
    public static function getAll(): Collection
    {
        $stmt = self::getDb()->query("SELECT * FROM users");
        $users = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = (int)$row['id'];
            
            // Используем Identity Map, если объект уже загружен
            if (isset(self::$identityMap[$id])) {
                $users[] = self::$identityMap[$id];
                continue;
            }
            
            // Создаем новый объект
            $user = new self($row['name'], $row['email']);
            $user->id = $id;
            
            // Сохраняем в Identity Map
            self::$identityMap[$id] = $user;
            $users[] = $user;
        }
        
        return new Collection($users);
    }
    
    /**
     * Найти пользователя по ID - использует Identity Map для предотвращения дублирования объектов
     */
    public static function find(int $id): ?self
    {
        // Сначала проверяем Identity Map
        if (isset(self::$identityMap[$id])) {
            return self::$identityMap[$id];
        }
        
        // Если нет в Identity Map, загружаем из БД
        $stmt = self::getDb()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        if (!$data) return null;
        
        $user = new self($data['name'], $data['email']);
        $user->id = $id;
        
        // Сохраняем в Identity Map
        self::$identityMap[$id] = $user;
        
        return $user;
    }
    
    /**
     * Сохранить пользователя, здесь походу это Active Record метод
     */
    public function save(): bool
    {
        $db = self::getDb();
        
        if ($this->id) {
            // Обновление существующей записи
            $stmt = $db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            return $stmt->execute([$this->name, $this->email, $this->id]);
        }
        
        // Вставка новой записи
        $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $result = $stmt->execute([$this->name, $this->email]);
        
        $this->id = (int)$db->lastInsertId();
        
        // Добавляем новый объект в Identity Map
        self::$identityMap[$this->id] = $this;
        
        return $result;
    }
    
    /**
     * Подключение к БД
     */
    private static function getDb(): PDO
    {
        static $db = null;
        
        if ($db === null) {
            $db = new PDO('mysql:host=localhost;dbname=patterns;charset=utf8mb4', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        return $db;
    }
    
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
}