<?php
// Запуск скрипта для суммирования
echo "<h2>Результат суммирования (Первое задание):</h2>";
$output = shell_exec('bash /data/mysite.local/sum.sh 5.5 -10');
echo "<pre>$output</pre>";

// Запуск скрипта для популярных городов
echo "<h2>3 наиболее популярных города (Второе задание):</h2>";
$output = shell_exec('bash /data/mysite.local/popular_cities.sh');
echo "<pre>$output</pre>";
