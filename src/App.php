<?php

class App
{
    private SessionManager $sessionManager;

    public function __construct()
    {
        require_once __DIR__.'/BracketValidator.php';
        require_once __DIR__.'/SessionManager.php';
        $this->sessionManager = new SessionManager;
    }

    public function run(): string
    {
        $this->sessionManager->updateSessionData();
        $sessionInfo = $this->sessionManager->getSessionInfo();

        header("X-session: Hostname: {$sessionInfo['hostname']} Request count: {$sessionInfo['count']}");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new RuntimeException('Доступны только POST-запросы.', 400);
        }

        if (! isset($_POST['string']) || $_POST['string'] === '') {
            throw new InvalidArgumentException("Параметр 'string' отсутствует или пуст.", 400);
        }

        $validator = new BracketValidator;

        if ($validator->isValid($_POST['string'])) {
            return 'Строка корректна.';
        }

        throw new RuntimeException('Строка некорректна.', 400);
    }
}
