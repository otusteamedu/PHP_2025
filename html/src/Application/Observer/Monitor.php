<?php

declare(strict_types=1);

namespace Otus\Food\Application\Observer;

use SplObserver;
use SplSubject;

final readonly class Monitor implements SplObserver
{
    private const string UNIX_SOCKET = 'monitor.sock';

    /**
     * @param Kitchen|SplSubject $subject
     */
    public function update(Kitchen|SplSubject $subject): void
    {
        $actor = null;
        $builder = null;

        if ($subject->getBegin()) {
            $actor = 'Begin';
            $builder = $subject->getBegin();
        }

        if ($subject->getEnd()) {
            $actor = 'End';
            $builder = $subject->getEnd();
        }

        if ($builder) {
            $content = sprintf(
                '%s : %s : %s : %s',
                $actor,
                $builder->getMeal()->getTitle(),
                $builder->getOrder()->getId(),
                $builder->getOrder()->getBuyer(),
            );

            $this->write($content);
        }
    }

    /**
     * @param string $content
     */
    private function write(string $content): void
    {
        file_put_contents(self::UNIX_SOCKET, $content . PHP_EOL, FILE_APPEND);
    }
}
