<?php

namespace Crowley\Hw\Presenters\Controllers;

use Crowley\Hw\Application\Services\TaskService;
use Crowley\Hw\Application\Services\UserService;
use Crowley\Hw\Infrastructure\Persistence\MySQLTaskRepository;
use Crowley\Hw\Infrastructure\Persistence\MySQLUserRepository;
use Crowley\Hw\Kernel\Database;

class HomeController extends BaseController
{

    public function index() {

        $this->pageTitle = 'Первая';

        //echo 'это страница /';

        $this->render('home/index');

    }

    public function verify_email()
    {
        $emails = (isset($_POST['emails'])) ? $_POST['emails'] : null;
        $data = array_filter(array_map('trim', explode("\n", $emails)));

        var_dump($data);
        echo '<br>';
        echo '<br>';

        $mx_records = [];

        $result = [];

        foreach ($data as $email) {

            // Проверяем синтаксис email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result[$email] = '❌ Невалидный формат';
                continue;
            }

            // Берём домен после @
            $domain = substr(strrchr($email, "@"), 1);

            // Проверяем MX-записи
            if (getmxrr($domain, $mx_records)) {
                $result[$email] = '✅ Домен принимает почту';
            } else {
                $result[$email] = '❌ Нет MX-записей';
            }
        }

        echo '<pre>';
        var_dump($result);
        echo '</pre>';

    }

    public function home()
    {

    }

}