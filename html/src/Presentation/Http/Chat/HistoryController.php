<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http\Chat;

use Otus\Queue\Application\UseCase\Chat\History;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;
use Otus\Queue\Infrastructure\Template\TemplateInterface;

final readonly class HistoryController
{
    /**
     * @param History $useCase
     * @param TemplateInterface $template
     */
    public function __construct(
        private History $useCase,
        private TemplateInterface $template,
    ) {
    }

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        $html = $this->template->render('chat/index', [
            'history' => $this->useCase->handle(),
        ]);

        return ResponseFactory::toHtml(body: $html);
    }
}
