<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Context\ContextDetector;
use App\Core\Container\Context\ContextType;

class ServiceProviderFactory
{
    public function __construct(
        private readonly ContextDetector $contextDetector,
    ) {
    }

    /**
     * @return ServiceProviderInterface[]
     */
    public function createProviders(): array
    {
        $providers[] = new CommonServiceProvider();

        $contextType = $this->contextDetector->getContextType();
        switch ($contextType) {
            case ContextType::CLI:
                $providers[] = new ConsoleServiceProvider();
                break;
            case ContextType::HTTP_API:
                $providers[] = new HttpServiceProvider();
                break;
            case ContextType::HTTP_WEB:
                $providers[] = new UiServiceProvider();
                $providers[] = new HttpServiceProvider();
                break;
            default:
                throw new \LogicException(
                    "Provider not found for context: '{$contextType->value}'",
                );
        }

        $providers[] = new ModuleServiceProvider();

        return $providers;
    }
}
