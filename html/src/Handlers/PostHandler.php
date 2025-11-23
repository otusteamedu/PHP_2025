<?php

declare(strict_types=1);

namespace Otus\Handlers;

use Otus\Kernel\Http\Request;
use Otus\Kernel\Http\Response;
use Otus\Strategy\CounterStrategy;
use Otus\Strategy\ReplaceStrategy;
use Otus\Strategy\StrategyInterface;
use Otus\Strategy\ValidatorContext;

readonly class PostHandler implements HandlerInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $validate = false;
        $string = (string) $request->post->get('string', '');

        $string = preg_replace('/[^\(\)]+/', '', $string);

        if (!empty($string)) {
            $strategy = $this->getStrategy();

            $validate = new ValidatorContext($strategy)->validate($string);
        }

        if ($validate) {
            return new Response('OK : ' . gethostname(), [
                'HTTP/1.1 200 OK',
            ]);
        }

        return new Response('Bad Request : ' . gethostname(), [
            'HTTP/1.1 400 Bad Request',
        ]);
    }

    /**
     * @return StrategyInterface
     */
    protected function getStrategy(): StrategyInterface
    {
        $strategies = $this->getStrategies();

        shuffle($strategies);

        return array_pop($strategies);
    }

    /**
     * @return array
     */
    protected function getStrategies(): array
    {
        return [
            new CounterStrategy(),
            new ReplaceStrategy(),
        ];
    }
}
