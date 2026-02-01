<?php

declare(strict_types=1);

namespace Otus\Food\Application\Observer;

use Otus\Food\Application\Builder\AbstractBuilder;
use SplObjectStorage;
use SplObserver;
use SplSubject;

final class Kitchen implements SplSubject
{
    /**
     * @var AbstractBuilder|null
     */
    private ?AbstractBuilder $begin = null;

    /**
     * @var AbstractBuilder|null
     */
    private ?AbstractBuilder $end = null;

    /**
     * @var SplObjectStorage
     */
    private readonly SplObjectStorage $list;

    public function __construct()
    {
        $this->list = new SplObjectStorage();
    }

    /**
     * @param SplObserver $observer
     */
    public function attach(SplObserver $observer): void
    {
        $this->list->offsetSet($observer);
    }

    /**
     * @param SplObserver $observer
     */
    public function detach(SplObserver $observer): void
    {
        $this->list->offsetSet($observer);
    }

    /**
     * @param AbstractBuilder $builder
     *
     * @return $this
     */
    public function begin(AbstractBuilder $builder): self
    {
        $this->begin = $builder;

        return $this;
    }

    /**
     * @param AbstractBuilder $builder
     *
     * @return $this
     */
    public function end(AbstractBuilder $builder): self
    {
        $this->end = $builder;

        return $this;
    }

    /**
     * @return AbstractBuilder|null
     */
    public function getBegin(): ?AbstractBuilder
    {
        return $this->begin;
    }

    /**
     * @return AbstractBuilder|null
     */
    public function getEnd(): ?AbstractBuilder
    {
        return $this->end;
    }

    public function notify(): void
    {
        /** @var SplObserver $observer */
        foreach ($this->list as $observer) {
            $observer->update($this);
        }
    }
}
