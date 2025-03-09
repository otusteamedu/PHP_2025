<?php

namespace App\Validation;

use App\Contracts\RequestPipeInterface;
use App\Exceptions\EmptyRequestException;
use App\Exceptions\InvalidRequestMethodException;
use App\Http\Request;

class FormRequest implements RequestPipeInterface
{
    private const string INVALID_REQUEST_METHOD_MESSAGE = 'Неверный метод запроса.';
    private const string EMPTY_REQUEST_MESSAGE = 'В запросе отсутствуют данные.';

    /**
     * @param Request $request
     * @param $next
     * @return mixed
     * @throws EmptyRequestException
     * @throws InvalidRequestMethodException
     */
    public function validate(Request $request, $next): mixed
    {
        if ($request->isPost() === false) {
            throw new InvalidRequestMethodException(self::INVALID_REQUEST_METHOD_MESSAGE);
        }

        $emails = $request->get('emails');

        if (empty($emails)) {
            throw new EmptyRequestException(self::EMPTY_REQUEST_MESSAGE);
        }

        return $next($request);
    }
}