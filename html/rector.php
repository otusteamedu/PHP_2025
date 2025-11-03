<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Php55\Rector\Class_\ClassConstantToSelfClassRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        'config',
        'public',
        'src',
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::PHP_84,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
    ])
    ->withSkip([
        SymplifyQuoteEscapeRector::class,
        RemoveUselessParamTagRector::class,
        RemoveUselessReturnTagRector::class,
        RemoveUselessVarTagRector::class,
        ReturnBinaryOrToEarlyReturnRector::class,
        ClassConstantToSelfClassRector::class,
        ClosureToArrowFunctionRector::class,
        NullToStrictStringFuncCallArgRector::class,
    ])
    ->withCache()
    ->withImportNames();
