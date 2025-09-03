<?php
namespace App\Application\UseCases\Commands\SendEmail;

use App\Infrastructure\Notifications\EmailSender;

class Handler
{
    private $sender;

    public function __construct()
    {
        $this->sender = new EmailSender;
    }

    public function handle($email, $startDate, $finishDate)
    {
        $this->sender->send($email, $startDate, $finishDate);
    }
}