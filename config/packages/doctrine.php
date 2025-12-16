<?php

declare(strict_types=1);

use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine): void {
    $dbal = $doctrine->dbal();

    $dbal = $dbal
        ->connection('default')
        ->dbname(\getenv('POSTGRES_DB'))
        ->host(\getenv('PROJECT_NAME') . '_' . \getenv('POSTGRES_CONTAINER_NAME'))
        ->port(\getenv('POSTGRES_PORT'))
        ->user(\getenv('POSTGRES_USER'))
        ->password(\getenv('POSTGRES_PASSWORD'))
        ->driver('pdo_pgsql')
        ->logging('%kernel.debug%')
        ->serverVersion('17.4');

    $orm = $doctrine->orm();

    $orm
        ->entityManager('default')
        ->connection('default')
        ->autoMapping(true)
//        ->metadataCacheDriver()->type('array')
//        ->queryCacheDriver()->type('array')
//        ->resultCacheDriver()->type('array')
//        ->namingStrategy('doctrine.orm.naming_strategy.default')
        ;

    $orm
        ->autoGenerateProxyClasses(false)
        ->proxyNamespace('Proxies')
        ->proxyDir('%kernel.cache_dir%/doctrine/orm/Proxies')
        ->defaultEntityManager('default');

};
