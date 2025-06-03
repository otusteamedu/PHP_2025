<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SessionStorageChecker;

class SessionController
{
    public function __construct()
    {
        ini_set('session.serialize_handler', 'php_serialize');
        session_start();
        $_SESSION['test_session_key_1'] ??= 'test_session_value_1';
        $_SESSION['test_session_key_2'] ??= 'test_session_value_2';
    }

    public function checkSessionStorage(): void
    {
        $sessionStorageChecker = new SessionStorageChecker();
        $sessionVars = $sessionStorageChecker->getSessionVarsFromStorage();

        include __DIR__ . '/../../templates/session-storage.php';
    }
}
