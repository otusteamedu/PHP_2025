<?php

namespace App;

use App\Database;

class User {
    private static $instances = [];
    private $id;
    private $name;
    private $email;

    private function __construct($id, $name, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public static function find($id) {
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            $user = new self($data['id'], $data['name'], $data['email']);
            self::$instances[$id] = $user;
            return $user;
        }

        return null;
    }

    public static function all($limit = 100, $offset = 0) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        $users = [];
        while ($data = $stmt->fetch()) {
            $users[] = new self($data['id'], $data['name'], $data['email']);
        }
    
        return $users;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }
}

