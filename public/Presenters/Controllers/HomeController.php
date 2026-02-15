<?php

namespace Crowley\Hw\Presenters\Controllers;

use Crowley\Hw\Application\Services\EmailService;
use Crowley\Hw\Application\Services\TaskService;
use Crowley\Hw\Application\Services\UserService;
use Crowley\Hw\Infrastructure\Persistence\EmailsRepository;
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
        $postEmails = (isset($_POST['emails'])) ? $_POST['emails'] : null;
        $data = array_filter(array_map('trim', explode("\n", $postEmails)));

        $emailsRepository = new EmailsRepository();
        $emailsService = new EmailService($emailsRepository);

        $emails = $emailsService->getMxRecords($data);

        $this->render('home/checkEmails', $emails);

    }

    public function home()
    {

    }

}