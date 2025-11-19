<?php

declare(strict_types=1);

namespace Otus\Handlers;

use DateTime;
use Otus\Kernel\Http\Request;
use Otus\Kernel\Http\Response;

readonly class GetHandler implements HandlerInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request): Response
    {
        if (empty($request->session->has('datetime'))) {
            $request->session->set('datetime', new DateTime()->format('Y-m-d'));
        }

        return new Response(
            'Container : ' . gethostname() . ' ; SESSION : ' . $request->session->get('datetime'),
        );
    }
}
