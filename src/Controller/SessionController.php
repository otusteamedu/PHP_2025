<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Response;
use App\Service\SessionStorageChecker;

class SessionController extends AbstractController
{
    public function __construct()
    {
        ini_set('session.serialize_handler', 'php_serialize');
        session_start();
        $_SESSION['test_session_key_1'] ??= 'test_session_value_1';
        $_SESSION['test_session_key_2'] ??= 'test_session_value_2';

        parent::__construct();
    }

    public function checkSessionStorage(): Response
    {
        $sessionStorageChecker = new SessionStorageChecker();
        $this->view->sessionVars = $sessionStorageChecker->getSessionVarsFromStorage();

        return $this->view->render('session-storage.php');
    }
}
