<?php
declare(strict_types=1);

require __DIR__ . '/../../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Application;
use Zibrov\OtusPhp2025\Database\Postgresql\Config as PostgresqlConfig;

?>

<p><-- <a href="/postgresql/">назад</a></p>

<p>Создать таблицу в Postgres</p>

<?php
$obPostgresqlConfig = new PostgresqlConfig();
$app = new Application($obPostgresqlConfig);
$app->createTable();
?>

<p>Tаблицs в Postgres созданы успешно!</p>

