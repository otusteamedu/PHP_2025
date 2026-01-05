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
    
    // Отслеживание по изменениям
    // Позволит обновить в БД только изменившиеся поля
    private ?string $originalName = null;
    private ?string $originalEmail = null;
    
    public function __construct(string $name = '', string $email = '')
    {
        $this->name = $name;
        $this->email = $email;
        
        // Сохраняем начальные значения как оригинальные
        $this->originalName = $name;
        $this->originalEmail = $email;
    }
    
    /**
     * Получить всех пользователей - возвращает коллекцию
     * Внимание: Для больших таблиц следует использовать пагинацию
     */
    public static function getAll(): Collection
    {
        $db = self::getDb();
        
        // Лимит от перегрузки памяти
        $limit = 1000;
        
        // Проверяем количество записей
        $countStmt = $db->query("SELECT COUNT(*) as cnt FROM users");
        $totalRows = (int)$countStmt->fetch()['cnt'];
        
        if ($totalRows > $limit) {
            // Ограничиваем выборку для больших таблиц
            $stmt = $db->prepare("SELECT * FROM users LIMIT ?");
            $stmt->execute([$limit]);
            
            // Логируем предупреждение
            error_log("Таблица users содержит $totalRows записей. Выборка ограничена $limit записями.");
        } else {
            $stmt = $db->query("SELECT * FROM users");
        }
        
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
            
            // ДОБАВЛЕНО: сохраняем значения из БД как оригинальные
            $user->originalName = $row['name'];
            $user->originalEmail = $row['email'];
            
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
        
        // Сохраняем значения из БД как оригинальные
        $user->originalName = $data['name'];
        $user->originalEmail = $data['email'];
        
        // Сохраняем в Identity Map
        self::$identityMap[$id] = $user;
        
        return $user;
    }
    
    /**
     * Сохранить пользователя, здесь походу это Active Record метод
     * Обновление только измененных полей для оптимизации запросов
     */
    public function save(): bool
    {
        $db = self::getDb();
        
        if ($this->id) {
            // Здесь собираем только измененные поля
            $updates = [];
            $params = [];
            
            // Проверяем, изменилось ли имя
            if ($this->name !== $this->originalName) {
                $updates[] = 'name = ?';
                $params[] = $this->name;
            }
            
            // Проверяем, изменился ли email
            if ($this->email !== $this->originalEmail) {
                $updates[] = 'email = ?';
                $params[] = $this->email;
            }
            
            // Если ничего не изменилось - выходим
            if (empty($updates)) {
                return true;
            }
            
            // Добавляем ID для WHERE условия
            $params[] = $this->id;
            
            // Формируем SQL с только измененными полями
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                // Обновляем оригинальные значения после успешного сохранения
                $this->originalName = $this->name;
                $this->originalEmail = $this->email;
            }
            
            return $result;
        }
        
        // Вставка новой записи
        $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $result = $stmt->execute([$this->name, $this->email]);
        
        if ($result) {
            $this->id = (int)$db->lastInsertId();
            
            // Обновляем оригинальные значения
            $this->originalName = $this->name;
            $this->originalEmail = $this->email;
            
            // Добавляем новый объект в Identity Map
            self::$identityMap[$this->id] = $this;
        }
        
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
    
    // Устанавливаем изменения значений
    public function setName(string $name): void 
    { 
        $this->name = $name; 
    }
    
    public function setEmail(string $email): void 
    { 
        $this->email = $email; 
    }
}