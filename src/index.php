<?php declare(strict_types=1);

use Konstantind\OtusFlexibleFloatParser\FlexibleFloatParser;

require __DIR__ . '/../vendor/autoload.php';

echo FlexibleFloatParser::parse('3,14') . PHP_EOL;
echo FlexibleFloatParser::parse('-2,8') . PHP_EOL;
echo FlexibleFloatParser::parse('.5') . PHP_EOL;
echo FlexibleFloatParser::parse('') . PHP_EOL;
echo FlexibleFloatParser::parse('abc') . PHP_EOL;
