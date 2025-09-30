<?php

require_once 'vendor/autoload.php';

use App\Container\DIContainer;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\Services\LoggerService;
use App\Services\NotificationService;
use App\Services\SubscriptionService;
use App\Entities\User;

// Настройка DI контейнера
$container = new DIContainer();

$container->set('db', function() {
    $config = require 'config/database.php';
    return new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
});

$container->set('userRepository', function($c) {
    return new UserRepository($c->get('db'));
});

$container->set('mailService', function() {
    return new MailService();
});

$container->set('logger', function() {
    return new LoggerService();
});

$container->set('notificationService', function($c) {
    return new NotificationService($c->get('mailService'), $c->get('logger'));
});

$container->set('subscriptionService', function($c) {
    return new SubscriptionService($c->get('userRepository'), $c->get('logger'));
});

// Использование
try {
    $userRepository = $container->get('userRepository');
    $mailService = $container->get('mailService');
    $notificationService = $container->get('notificationService');
    $subscriptionService = $container->get('subscriptionService');
    $logger = $container->get('logger');
    
    // Создание пользователя
    $user = new User(null, "John Doe", "john@example.com");
    $user = $userRepository->save($user);
    
    $mailService->sendWelcomeEmail($user->email, $user->name);
    $logger->log("User created: " . $user->name);
    
    // Обновление подписки
    $subscriptionService->upgradeSubscription($user->id, 'premium');
    
    // Отправка уведомления
    $notificationService->sendNotification($user->email, $user->name, 'premium', "Your subscription has been upgraded!");
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}