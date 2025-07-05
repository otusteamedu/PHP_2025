<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Config\DoctrineConfig;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework, DoctrineConfig $doctrine): void {
    $framework->session()
        // Enables session support. Note that the session will ONLY be started if you read or write from it.
        // Remove or comment this section to explicitly disable session support.
        ->enabled(true)
        // ID of the service used for session storage
        // NULL means that Symfony uses PHP default session mechanism
        ->handlerId(null)
        // improves the security of the cookies used for sessions
        ->cookieSecure('auto')
        ->cookieSamesite(Cookie::SAMESITE_LAX)
        ->storageFactoryId('session.storage.factory.native')
    ;

    /*$framework
        ->cache()
        ->pool('doctrine.result_cache_pool')
        ->adapters('cache.app')
        ->pool('doctrine.system_cache_pool')
        ->adapters('cache.sytsem');

    $doctrine->orm()
        // ...
        ->entityManager('default')
        ->metadataCacheDriver()
        ->type('pool')
        ->pool('doctrine.system_cache_pool')
        ->queryCacheDriver()
        ->type('pool')
        ->pool('doctrine.system_cache_pool')
        ->resultCacheDriver()
        ->type('pool')
        ->pool('doctrine.result_cache_pool')

        // in addition to Symfony cache pools, you can also use the
        // 'type: service' option to use any service as a cache pool
        ->queryCacheDriver()
        ->type('service')
        ->id(App\ORM\MyCacheService::class)
    ;*/
};
