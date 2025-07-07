<?php

namespace Application\Validators;

use Application\Http\Request;
use Infrastructure\Exceptions\ValidationException;

class Validator
{
    /** @var Request */
    protected Request $request;

    /** @var string|null */
    protected ?string $message;

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function validate(): void {
        $route = $this->request->getRoute();

        if (method_exists($this, $route['method'])) {
            $result = $this->{$route['method']}();

            if ($result === false) {
                $this->fail();
            }
        }
    }

    /**
     * @param string|null $message
     * @return mixed
     * @throws ValidationException
     */
    public function fail(?string $message = "Произошла валидационная ошибка"): mixed {
        if (empty($this->message) === false) {
            $message = $this->message;
        }

        throw new ValidationException($message);
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void {
        $this->message = $message;
    }
}