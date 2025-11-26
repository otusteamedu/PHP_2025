<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\MethodNotAllowedException;
use App\Exception\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Service\VerificationService;

class VerificationController
{
    private const POST_INPUT_KEY = 'string';

    private VerificationService $verifyService;

    public function __construct(VerificationService $verifyService)
    {
        $this->verifyService = $verifyService;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws MethodNotAllowedException
     * @throws ValidationException
     */
    public function handle(Request $request): Response
    {
        if ($request->getServerRequestMethod() !== 'POST') {
            throw new MethodNotAllowedException();
        }

        $string = $request->getPostInputByKey(self::POST_INPUT_KEY);
        $this->verifyService->validate($string);

        return new Response(200, 'OK');
    }
}
