<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http\Swagger;

use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;
use Otus\Queue\Infrastructure\Template\TemplateInterface;

final readonly class UiController
{
    /**
     * @param TemplateInterface $template
     */
    public function __construct(
        private TemplateInterface $template,
    ) {
    }

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        $urls = [
            [
                'name' => 'Swagger',
                'url' => '/swagger/api',
            ],
        ];

        $html = $this->template->render(
            'swagger/index',
            [
                'urls' => $urls,
            ],
        );

        return ResponseFactory::toHtml(
            body: $html,
        );
    }
}
