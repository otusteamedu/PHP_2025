<?php declare(strict_types=1);

namespace App;

use App\Exception\HttpException;
use App\Exception\MethodNotAllowedException;
use App\Exception\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Service\EmailValidationService;

class App
{
    private Request $request;
    private Response $response;
    private EmailValidationService $emailValidationService;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->emailValidationService = new EmailValidationService();
    }

    public function run(): Response
    {
        try {
            if ($this->request->getMethod() !== 'POST') {
                throw new MethodNotAllowedException('POST method not allowed');
            }

            $emails = $this->request->getPostValue('emails');

            if (empty($emails)) {
                throw new ValidationException('Emails should not be empty');
            }

            if (is_string($emails)) {
                $emails = array_map('trim', explode(';', $emails));
                $emails = array_filter($emails, fn($email) => $email !== '');
            }

            if (!is_array($emails)) {
                throw new ValidationException('Emails should be an array or a semicolon-separated string');
            }

            $validEmails = [];
            $invalidEmails = [];

            foreach ($emails as $email) {
                if ($this->emailValidationService->validate($email)) {
                    $validEmails[] = $email;
                } else {
                    $invalidEmails[] = $email;
                }
            }

            return $this->response->send(200, [
                'emails' => [
                    'valid' => $validEmails,
                    'invalid' => $invalidEmails,
                ]
            ]);
        } catch (HttpException $e) {
            return $this->response->send($e->getHttpCode(), [
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return $this->response->send(500, [
                'message' => 'Internal server error',
            ]);
        }
    }
}
