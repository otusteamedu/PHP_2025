<?php

class User {
    public $id;
    public $name;
    public $email;
    public $subscriptionType;
    
    public function __construct($id, $name, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->subscriptionType = 'free';
    }
    
    public function createUser() {
        // Создание пользователя в базе данных
        $db = new mysqli('localhost', 'user', 'pass', 'app');
        $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $this->name, $this->email);
        $stmt->execute();
        $this->id = $stmt->insert_id;
        
        // Отправка email
        mail($this->email, "Welcome", "Hello " . $this->name);
        
        // Логирование
        file_put_contents('log.txt', "User created: " . $this->name . "\n", FILE_APPEND);
    }
    
    public function upgradeSubscription($type) {
        if ($type == 'premium' || $type == 'vip') {
            $this->subscriptionType = $type;
            
            $db = new mysqli('localhost', 'user', 'pass', 'app');
            $stmt = $db->prepare("UPDATE users SET subscription = ? WHERE id = ?");
            $stmt->bind_param("si", $type, $this->id);
            $stmt->execute();
            
            file_put_contents('log.txt', "Subscription upgraded: " . $this->name . " to " . $type . "\n", FILE_APPEND);
        }
    }
    
    public function sendNotification($message) {
        // Отправка уведомления разными способами
        if ($this->subscriptionType == 'premium') {
            // Отправка SMS
            echo "SMS to " . $this->name . ": " . $message . "\n";
        }
        mail($this->email, "Notification", $message);
        file_put_contents('log.txt', "Notification sent: " . $message . "\n", FILE_APPEND);
    }
}

// Использование
$user = new User(null, "John Doe", "john@example.com");
$user->createUser();
$user->upgradeSubscription('premium');
$user->sendNotification("Your subscription has been upgraded!");