<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\EmailValidatorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmailController extends AbstractController
{
   private EmailValidatorService $emailValidatorService;

    public function __construct(EmailValidatorService $emailValidatorService)
    {
        $this->emailValidatorService = $emailValidatorService;
    }

    #[Route('/check-emails', methods: ['POST'])]
    public function checkEmails(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['emails'])  || !is_array($data['emails'])) {
            return new JsonResponse(['error' => 'Передайте массив email-ов'], 400);
        }

        $results = $this->emailValidatorService->validateEmails($data['emails']);
        return new JsonResponse($results, 200);
    }
}