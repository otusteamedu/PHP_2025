<?php

$env = require __DIR__ . '/env.php';

$appName = getAppName();
$appDebug = getAppDebugMode();
$userMarks = getUserMarks();

echo "App name: " . $appName . "<br>";
var_dump($appName);
echo "<br><br>";

echo "Debug mode: " . ($appDebug ? 'ON' : 'OFF') . "<br>";
var_dump($appDebug);
echo "<br><br>";

echo "Debug mode: " . implode(",", $userMarks) . "<br>";
var_dump($userMarks);
