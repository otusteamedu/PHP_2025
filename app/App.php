<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025;

use \JsonException;
use Zibrov\OtusPhp2025\Http\Exceptions\HttpException;
use Zibrov\OtusPhp2025\Http\Exceptions\MethodNotAllowed;
use Zibrov\OtusPhp2025\Http\Request;
use Zibrov\OtusPhp2025\Http\Response;
use Zibrov\OtusPhp2025\Validators\ValidationException;
use Zibrov\OtusPhp2025\Validators\ValidationEmail;
use Zibrov\OtusPhp2025\Validators\ValidationService;

class App
{

    private Request $request;
    private Response $response;
    private ValidationService $validationService;

    public function __construct(bool $isRequiredValidateDNS = false, bool $isIDN = false)
    {
        $this->request = new Request();
        $this->response = new Response();

        $this->validationService = new ValidationService();
        $this->validationService->setIsRequiredValidateDNS($isRequiredValidateDNS);
        $this->validationService->setIsIDN($isIDN);
    }

    /**
     * @throws JsonException
     */
    public function run(): Response
    {
        try {
            if (!$this->request->isPostMethod()) {
                throw new MethodNotAllowed('POST method not allowed');
            }

            $arValues = $this->request->getValuesByKey('emails');

            $validationEmail = new ValidationEmail();
            $validationEmail->setValidationService($this->validationService);

            if ($arValidationEmails = $validationEmail->checkingEmailList($arValues)) {
                return $this->response->send(200, $arValidationEmails);
            }

            throw new ValidationException('Value is empty or does not exist');

        } catch (HttpException $e) {
            return $this->response->send($e->getHttpCode(), [
                'message' => $e->getMessage(),
            ]);
        }
    }
}