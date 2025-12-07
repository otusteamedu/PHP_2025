<?php
declare(strict_types=1);

namespace App;

use App\Controller\EmailVerificationController;
use App\Http\Request;


class Application
{
    private EmailVerificationController $emailVerificationController;

    /**
     * @param EmailVerificationController $emailVerificationController
     */
    public function __construct(EmailVerificationController $emailVerificationController)
    {
        $this->emailVerificationController = $emailVerificationController;
    }

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self(EmailVerificationController::create());
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $response = $this->emailVerificationController->handle(new Request());
        $response->sendHeaders();
        echo $response->getContent();
    }
}
