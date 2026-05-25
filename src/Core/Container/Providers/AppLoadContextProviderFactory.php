<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Context\AppLoadContext;

class AppLoadContextProviderFactory
{
    /**
     * @return ServiceProviderInterface[]
     */
    public function createProviders(AppLoadContext $appLoadContext): array
    {
        $providers = [];
        switch ($appLoadContext) {
            case AppLoadContext::CLI:
                $providers[] = new ConsoleServiceProvider();
                break;
            case AppLoadContext::HTTP_API:
                $providers[] = new HttpServiceProvider();
                break;
            case AppLoadContext::HTTP_WEB:
                $providers[] = new UiServiceProvider();
                $providers[] = new HttpServiceProvider();
                break;
            default:
                throw new \RuntimeException("Provider not found for context: '$appLoadContext->value'");
        }

        return $providers;
    }
}
