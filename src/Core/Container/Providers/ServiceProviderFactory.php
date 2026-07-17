<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Config\ContainerConfig;
use App\Core\Container\Context\ContextDetector;

class ServiceProviderFactory
{
    public function __construct(
        private readonly ContextDetector $contextDetector,
        private readonly ContainerConfig $config,
    ) {
    }

    /**
     * @return ServiceProviderInterface[]
     */
    public function createProviders(): array
    {
        $strategy = $this->contextDetector->detectStrategy();

        $providerClasses = array_merge(
            $this->config->getDefaultProviders(),
            $strategy->getSpecificProviders(),
        );

        $providers = array_map(static fn(string $class) => new $class(), $providerClasses);

        usort($providers, $this->buildSortComparator());

        return $providers;
    }

    private function buildSortComparator(): callable
    {
        $priorities = $this->config->getProviderPriorities();

        return static function (
            ServiceProviderInterface $a,
            ServiceProviderInterface $b,
        ) use ($priorities) {
            $classA = get_class($a);
            $classB = get_class($b);

            $priorityA = $priorities[$classA] ?? 0;
            $priorityB = $priorities[$classB] ?? 0;

            return $priorityB <=> $priorityA;
        };
    }
}
