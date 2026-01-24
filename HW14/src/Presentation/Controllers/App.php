<?php

namespace App\Presentation\Controllers;

use App\Infrastructure\Container\ContainerBuilder;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class App
{
    private EmailVerificationController $emailVerificationController;

    public function __construct(EmailVerificationController $emailVerificationController)
    {
        $this->emailVerificationController = $emailVerificationController;
    }

    /**
     * @return self
     * @throws \Exception
     */
    public static function build(): self
    {
        $container = ContainerBuilder::build();
        return $container->get(App::class);
    }

    /**
     * @return Response
     */
    public function run(): Response
    {
        $request = Request::fromGlobals();
        return $this->emailVerificationController->handleRequest($request);
    }
}
