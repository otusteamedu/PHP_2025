<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http\Chat;

use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;
use Otus\Queue\Infrastructure\Template\TemplateInterface;

final readonly class IndexController
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
        $html = $this->template->render('chat/index');

        return ResponseFactory::toHtml(
            body: $html,
        );
    }
}
