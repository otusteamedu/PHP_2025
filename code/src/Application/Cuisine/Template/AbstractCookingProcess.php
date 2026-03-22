<?php

declare(strict_types=1);

namespace Otus\Code\Application\Cuisine\Template;

use Otus\Code\Domain\Product\Contract\ProductInterface;

abstract class AbstractCookingProcess
{
    /**
     * @var string[]
     */
    private array $log = [];

    final public function cook(ProductInterface $product): CookingResult
    {
        $this->log = [];

        $this->beforeCooking($product);
        $this->prepare($product);
        $this->cookCore($product);

        if (!$this->meetsStandard($product)) {
            $this->afterFailedQualityCheck($product);

            return new CookingResult(false, 'utilized', $this->log);
        }

        $this->afterSuccessfulCooking($product);

        return new CookingResult(true, 'ready_to_serve', $this->log);
    }

    protected function beforeCooking(ProductInterface $product): void
    {
        $this->log[] = 'Pre-event: cuisine accepted ' . $product->getName();
    }

    protected function prepare(ProductInterface $product): void
    {
        $this->log[] = 'Preparing ingredients for ' . $product->getName();
    }

    abstract protected function cookCore(ProductInterface $product): void;

    abstract protected function meetsStandard(ProductInterface $product): bool;

    protected function afterSuccessfulCooking(ProductInterface $product): void
    {
        $this->log[] = 'Post-event: ' . $product->getName() . ' passed quality check';
    }

    protected function afterFailedQualityCheck(ProductInterface $product): void
    {
        $this->log[] = 'Post-event: ' . $product->getName() . ' failed quality check and was utilized';
    }

    protected function writeLog(string $message): void
    {
        $this->log[] = $message;
    }
}
