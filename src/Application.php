<?php
declare(strict_types=1);

namespace App;

use App\Controller\EmailVerificationController;
use App\Http\Request;
use App\Http\Response;


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
     * @return Response
     */
    public function run(): Response
    {
        return $this->emailVerificationController->handle(new Request());
    }
}
