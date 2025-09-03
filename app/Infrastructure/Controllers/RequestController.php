<?php

namespace App\Infrastructure\Controllers;

use App\Views\RequestTemplate;
use App\Views\NoteTemplate;
use App\Application\UseCases\Commands\SendRequest\Handler;

class RequestController
{
    private $sendRequestHandler;

    public function __construct()
    {
        $this->sendRequestHandler = new Handler();
    }

    public function showForm()
    {
        $template = new RequestTemplate([]);
        echo $template->render();
    }

    public function showNote()
    {
        $request = $_POST;

        $this->sendRequestHandler->handle($request);

        $beginDate = date('d.m.Y', strtotime($request['date_start']));
        $endDate = date('d.m.Y', strtotime($request['date_finish']));
        $data = [
            'beginDate' => $beginDate,
            'endDate' => $endDate
        ];
        $template = new NoteTemplate($data);
        echo $template->render();
    }
}