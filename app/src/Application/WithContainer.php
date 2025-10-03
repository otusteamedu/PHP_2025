<?php
declare(strict_types=1);

namespace App\Application;

use PHPUnit\Framework\Attributes\Before;
use Psr\Container\ContainerInterface;
use function set_time_limit;

trait WithContainer
{
    /** @internal */
    private ?ContainerInterface $di = null;

    #[Before]
    public function setUpWithDi(): void
    {
        set_time_limit(3600);
        if ($this->di) {
            return;
        }

        $this->di = Application::initialize();
    }

    /** @param array<mixed> $partialDependencies */
    public function createFromContainer(string $className, array $partialDependencies = []): mixed
    {
        return $this->di->make($className, $partialDependencies);
    }

    public function putInContainer(string $key, mixed $value): void
    {
        $this->di->set($key, $value);
    }

    public function injectInContainer(string $key, mixed $value): void
    {
        $this->putInContainer($key, $value);
    }
}
